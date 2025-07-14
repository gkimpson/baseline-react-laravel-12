# Frontend Guidelines
## Pro Clubs - Development Standards & Best Practices

---

### **Document Information**
**Application:** Pro Clubs  
**Version:** 1.0  
**Date:** June 14, 2025  
**Document Type:** Frontend Development Guidelines

---

## **1. Overview**

This document establishes comprehensive frontend development guidelines for Pro Clubs, ensuring consistency, maintainability, and high-quality code across the entire application. These guidelines cover React development, TypeScript usage, styling with Tailwind CSS, and general best practices.

---

## **2. Project Structure & Organization**

### **2.1 Directory Structure**
```
resources/js/
├── Components/           # Reusable UI components
│   ├── ui/              # Base UI components (Button, Input, etc.)
│   ├── forms/           # Form-specific components
│   ├── charts/          # Data visualization components
│   └── layout/          # Layout components
├── Pages/               # Inertia.js page components
│   ├── Auth/            # Authentication pages
│   ├── Dashboard/       # Dashboard pages
│   ├── Players/         # Player management pages
│   ├── Clubs/           # Club management pages
│   └── Matches/         # Match analysis pages
├── Layouts/             # Page layout components
├── Hooks/               # Custom React hooks
├── Utils/               # Utility functions
├── Types/               # TypeScript type definitions
├── Stores/              # Zustand stores
└── Constants/           # Application constants
```

### **2.2 File Naming Conventions**
- **Components**: PascalCase (e.g., `PlayerCard.tsx`)
- **Pages**: PascalCase (e.g., `PlayerProfile.tsx`)
- **Hooks**: camelCase with 'use' prefix (e.g., `usePlayerData.ts`)
- **Utilities**: camelCase (e.g., `formatPlayerStats.ts`)
- **Types**: PascalCase (e.g., `PlayerTypes.ts`)
- **Constants**: SCREAMING_SNAKE_CASE (e.g., `API_ENDPOINTS.ts`)

### **2.3 Import Organization**
```typescript
// 1. React and external libraries
import React, { useState, useEffect } from 'react'
import { motion } from 'framer-motion'
import { useQuery } from '@tanstack/react-query'

// 2. Internal utilities and hooks
import { usePlayerData } from '@/Hooks/usePlayerData'
import { formatPlayerStats } from '@/Utils/formatters'

// 3. Components (UI first, then custom)
import { Button } from '@/Components/ui/Button'
import { PlayerCard } from '@/Components/PlayerCard'

// 4. Types and constants
import type { Player } from '@/Types/PlayerTypes'
import { API_ENDPOINTS } from '@/Constants/api'
```

---

## **3. React Development Standards**

### **3.1 Component Structure**
```typescript
// Good: Functional component with proper structure
interface PlayerCardProps {
  player: Player
  onClick?: (player: Player) => void
  className?: string
}

export const PlayerCard: React.FC<PlayerCardProps> = ({
  player,
  onClick,
  className = ''
}) => {
  const [isHovered, setIsHovered] = useState(false)
  
  const handleClick = useCallback(() => {
    onClick?.(player)
  }, [onClick, player])

  return (
    <motion.div
      className={cn('player-card', className)}
      onHoverStart={() => setIsHovered(true)}
      onHoverEnd={() => setIsHovered(false)}
      onClick={handleClick}
    >
      <PlayerAvatar src={player.avatar} alt={player.name} />
      <PlayerInfo player={player} />
      <PlayerStats stats={player.stats} />
    </motion.div>
  )
}
```

### **3.2 Hooks Usage**
```typescript
// Custom hook for player data
export const usePlayerData = (playerId: string) => {
  return useQuery({
    queryKey: ['player', playerId],
    queryFn: () => fetchPlayer(playerId),
    staleTime: 5 * 60 * 1000, // 5 minutes
    retry: 3,
    retryDelay: attemptIndex => Math.min(1000 * 2 ** attemptIndex, 30000)
  })
}

// Usage in component
const PlayerProfile = ({ playerId }: { playerId: string }) => {
  const { data: player, isLoading, error } = usePlayerData(playerId)
  
  if (isLoading) return <PlayerSkeleton />
  if (error) return <ErrorMessage error={error} />
  if (!player) return <NotFound />
  
  return <PlayerDetails player={player} />
}
```

### **3.3 State Management**
```typescript
// Zustand store for global state
interface AppState {
  user: User | null
  theme: 'light' | 'dark'
  selectedClub: Club | null
  setUser: (user: User | null) => void
  setTheme: (theme: 'light' | 'dark') => void
  setSelectedClub: (club: Club | null) => void
}

export const useAppStore = create<AppState>((set) => ({
  user: null,
  theme: 'light',
  selectedClub: null,
  setUser: (user) => set({ user }),
  setTheme: (theme) => set({ theme }),
  setSelectedClub: (selectedClub) => set({ selectedClub })
}))
```

### **3.4 Error Boundaries**
```typescript
// Error boundary for catching React errors
export class ErrorBoundary extends React.Component<
  { children: React.ReactNode; fallback: React.ComponentType<{ error: Error }> },
  { hasError: boolean; error: Error | null }
> {
  constructor(props: any) {
    super(props)
    this.state = { hasError: false, error: null }
  }

  static getDerivedStateFromError(error: Error) {
    return { hasError: true, error }
  }

  componentDidCatch(error: Error, errorInfo: React.ErrorInfo) {
    console.error('Error caught by boundary:', error, errorInfo)
  }

  render() {
    if (this.state.hasError) {
      const FallbackComponent = this.props.fallback
      return <FallbackComponent error={this.state.error!} />
    }

    return this.props.children
  }
}
```

---

## **4. TypeScript Guidelines**

### **4.1 Type Definitions**
```typescript
// Comprehensive type definitions
export interface Player {
  id: string
  name: string
  position: Position
  overall: number
  attributes: PlayerAttributes
  club?: Club
  stats: PlayerStats
  createdAt: string
  updatedAt: string
}

export interface PlayerAttributes {
  pace: number
  shooting: number
  passing: number
  dribbling: number
  defending: number
  physicality: number
  goalkeeping?: GoalkeeperAttributes
}

export type Position = 
  | 'GK' 
  | 'CB' 
  | 'LB' 
  | 'RB' 
  | 'CDM' 
  | 'CM' 
  | 'CAM' 
  | 'LM' 
  | 'RM' 
  | 'LW' 
  | 'RW' 
  | 'ST'

// Utility types for API responses
export type ApiResponse<T> = {
  data: T
  message: string
  status: 'success' | 'error'
}

export type PaginatedResponse<T> = ApiResponse<{
  items: T[]
  pagination: {
    page: number
    perPage: number
    total: number
    totalPages: number
  }
}>
```

### **4.2 Props and Component Types**
```typescript
// Base props for common patterns
interface BaseComponentProps {
  className?: string
  children?: React.ReactNode
  'data-testid'?: string
}

// Extending base props
interface ButtonProps extends BaseComponentProps {
  variant?: 'primary' | 'secondary' | 'outline' | 'ghost'
  size?: 'sm' | 'md' | 'lg'
  disabled?: boolean
  loading?: boolean
  onClick?: () => void
}

// Generic component props
interface DataTableProps<T> extends BaseComponentProps {
  data: T[]
  columns: Column<T>[]
  onRowClick?: (item: T) => void
  loading?: boolean
  pagination?: PaginationProps
}
```

### **4.3 API Types**
```typescript
// Request/Response types
export interface CreatePlayerRequest {
  name: string
  position: Position
  eaPlayerId?: string
  platform: Platform
}

export interface UpdatePlayerRequest extends Partial<CreatePlayerRequest> {
  id: string
}

export interface PlayerSearchFilters {
  name?: string
  position?: Position[]
  club?: string
  platform?: Platform[]
  minOverall?: number
  maxOverall?: number
}
```

---

## **5. Styling Guidelines (Tailwind CSS)**

### **5.1 Class Organization**
```typescript
// Good: Logical grouping of classes
<div className={cn(
  // Layout
  'flex items-center justify-between',
  // Spacing
  'p-4 gap-3',
  // Visual
  'bg-white border border-gray-200 rounded-lg shadow-sm',
  // Interactive
  'hover:shadow-md transition-shadow duration-200',
  // Responsive
  'md:p-6 lg:gap-4',
  // Conditional
  isActive && 'ring-2 ring-blue-500',
  className
)}>
```

### **5.2 Custom Component Styling**
```typescript
// Component with variant styles
const buttonVariants = {
  primary: 'bg-blue-600 text-white hover:bg-blue-700',
  secondary: 'bg-gray-200 text-gray-900 hover:bg-gray-300',
  outline: 'border border-gray-300 text-gray-700 hover:bg-gray-50',
  ghost: 'text-gray-600 hover:bg-gray-100'
}

const sizeVariants = {
  sm: 'px-3 py-1.5 text-sm',
  md: 'px-4 py-2 text-base',
  lg: 'px-6 py-3 text-lg'
}

export const Button: React.FC<ButtonProps> = ({
  variant = 'primary',
  size = 'md',
  className,
  children,
  ...props
}) => {
  return (
    <button
      className={cn(
        'inline-flex items-center justify-center font-medium rounded-md',
        'focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500',
        'disabled:opacity-50 disabled:cursor-not-allowed',
        'transition-colors duration-200',
        buttonVariants[variant],
        sizeVariants[size],
        className
      )}
      {...props}
    >
      {children}
    </button>
  )
}
```

### **5.3 Responsive Design**
```typescript
// Mobile-first responsive design
<div className={cn(
  // Mobile (default)
  'grid grid-cols-1 gap-4 p-4',
  // Tablet
  'md:grid-cols-2 md:gap-6 md:p-6',
  // Desktop
  'lg:grid-cols-3 lg:gap-8 lg:p-8',
  // Large screens
  'xl:grid-cols-4 2xl:gap-12'
)}>
```

### **5.4 Dark Mode Support**
```typescript
// Dark mode classes
<div className={cn(
  'bg-white text-gray-900',
  'dark:bg-gray-900 dark:text-gray-100',
  'border border-gray-200 dark:border-gray-700'
)}>
```

---

## **6. Performance Optimization**

### **6.1 Code Splitting**
```typescript
// Lazy loading for route components
const PlayerProfile = lazy(() => import('@/Pages/Players/PlayerProfile'))
const ClubDashboard = lazy(() => import('@/Pages/Clubs/ClubDashboard'))

// Lazy loading with loading fallback
<Suspense fallback={<PageSkeleton />}>
  <PlayerProfile playerId={playerId} />
</Suspense>
```

### **6.2 Memoization**
```typescript
// Memoize expensive calculations
const PlayerStatsChart = memo(({ stats, timeRange }: PlayerStatsChartProps) => {
  const chartData = useMemo(() => 
    processStatsForChart(stats, timeRange),
    [stats, timeRange]
  )

  return <Chart data={chartData} />
})

// Memoize callbacks to prevent unnecessary re-renders
const PlayerList = ({ players, onPlayerSelect }: PlayerListProps) => {
  const handlePlayerClick = useCallback((player: Player) => {
    onPlayerSelect(player)
  }, [onPlayerSelect])

  return (
    <div>
      {players.map(player => (
        <PlayerCard
          key={player.id}
          player={player}
          onClick={handlePlayerClick}
        />
      ))}
    </div>
  )
}
```

### **6.3 Virtual Scrolling**
```typescript
// Virtual scrolling for large lists
import { FixedSizeList as List } from 'react-window'

const PlayerListVirtualized = ({ players }: { players: Player[] }) => {
  const Row = ({ index, style }: { index: number; style: CSSProperties }) => (
    <div style={style}>
      <PlayerCard player={players[index]} />
    </div>
  )

  return (
    <List
      height={600}
      itemCount={players.length}
      itemSize={120}
      overscanCount={5}
    >
      {Row}
    </List>
  )
}
```

---

## **7. Form Handling**

### **7.1 Form Validation with React Hook Form**
```typescript
import { useForm } from 'react-hook-form'
import { zodResolver } from '@hookform/resolvers/zod'
import { z } from 'zod'

const playerSchema = z.object({
  name: z.string().min(2, 'Name must be at least 2 characters'),
  position: z.enum(['GK', 'CB', 'LB', 'RB', 'CDM', 'CM', 'CAM', 'LM', 'RM', 'LW', 'RW', 'ST']),
  eaPlayerId: z.string().optional(),
  platform: z.enum(['pc', 'playstation', 'xbox', 'switch'])
})

type PlayerFormData = z.infer<typeof playerSchema>

export const PlayerForm = ({ onSubmit, initialData }: PlayerFormProps) => {
  const {
    register,
    handleSubmit,
    formState: { errors, isSubmitting },
    reset
  } = useForm<PlayerFormData>({
    resolver: zodResolver(playerSchema),
    defaultValues: initialData
  })

  const handleFormSubmit = async (data: PlayerFormData) => {
    try {
      await onSubmit(data)
      reset()
    } catch (error) {
      console.error('Form submission error:', error)
    }
  }

  return (
    <form onSubmit={handleSubmit(handleFormSubmit)} className="space-y-4">
      <div>
        <label htmlFor="name" className="block text-sm font-medium text-gray-700">
          Player Name
        </label>
        <input
          {...register('name')}
          type="text"
          className={cn(
            'mt-1 block w-full rounded-md border-gray-300 shadow-sm',
            'focus:border-blue-500 focus:ring-blue-500',
            errors.name && 'border-red-300 focus:border-red-500 focus:ring-red-500'
          )}
        />
        {errors.name && (
          <p className="mt-1 text-sm text-red-600">{errors.name.message}</p>
        )}
      </div>
      
      <Button type="submit" disabled={isSubmitting}>
        {isSubmitting ? 'Saving...' : 'Save Player'}
      </Button>
    </form>
  )
}
```

---

## **8. Animation Guidelines**

### **8.1 Framer Motion Usage**
```typescript
// Page transitions
const pageVariants = {
  initial: { opacity: 0, y: 20 },
  animate: { opacity: 1, y: 0 },
  exit: { opacity: 0, y: -20 }
}

export const AnimatedPage = ({ children }: { children: React.ReactNode }) => (
  <motion.div
    variants={pageVariants}
    initial="initial"
    animate="animate"
    exit="exit"
    transition={{ duration: 0.3, ease: 'easeInOut' }}
  >
    {children}
  </motion.div>
)

// Hover animations
const cardVariants = {
  hover: {
    scale: 1.02,
    boxShadow: '0 10px 25px rgba(0, 0, 0, 0.1)',
    transition: { duration: 0.2 }
  }
}

export const AnimatedCard = ({ children }: { children: React.ReactNode }) => (
  <motion.div
    variants={cardVariants}
    whileHover="hover"
    className="bg-white rounded-lg border"
  >
    {children}
  </motion.div>
)
```

### **8.2 Loading States**
```typescript
// Skeleton loading component
export const PlayerCardSkeleton = () => (
  <div className="animate-pulse">
    <div className="flex items-center space-x-4 p-4">
      <div className="w-12 h-12 bg-gray-300 rounded-full"></div>
      <div className="flex-1">
        <div className="h-4 bg-gray-300 rounded w-3/4 mb-2"></div>
        <div className="h-3 bg-gray-300 rounded w-1/2"></div>
      </div>
    </div>
  </div>
)

// Staggered list animations
const listVariants = {
  hidden: { opacity: 0 },
  visible: {
    opacity: 1,
    transition: {
      staggerChildren: 0.1
    }
  }
}

const itemVariants = {
  hidden: { opacity: 0, y: 20 },
  visible: { opacity: 1, y: 0 }
}

export const AnimatedList = ({ items }: { items: any[] }) => (
  <motion.div variants={listVariants} initial="hidden" animate="visible">
    {items.map((item, index) => (
      <motion.div key={index} variants={itemVariants}>
        {item}
      </motion.div>
    ))}
  </motion.div>
)
```

---

## **9. Testing Guidelines**

### **9.1 Component Testing**
```typescript
import { render, screen, fireEvent } from '@testing-library/react'
import { PlayerCard } from '@/Components/PlayerCard'
import { mockPlayer } from '@/Utils/testUtils'

describe('PlayerCard', () => {
  it('displays player information correctly', () => {
    const player = mockPlayer()
    render(<PlayerCard player={player} />)
    
    expect(screen.getByText(player.name)).toBeInTheDocument()
    expect(screen.getByText(player.position)).toBeInTheDocument()
    expect(screen.getByText(player.overall.toString())).toBeInTheDocument()
  })

  it('calls onClick when clicked', () => {
    const player = mockPlayer()
    const handleClick = jest.fn()
    
    render(<PlayerCard player={player} onClick={handleClick} />)
    fireEvent.click(screen.getByRole('button'))
    
    expect(handleClick).toHaveBeenCalledWith(player)
  })
})
```

### **9.2 Hook Testing**
```typescript
import { renderHook, waitFor } from '@testing-library/react'
import { usePlayerData } from '@/Hooks/usePlayerData'
import { QueryClient, QueryClientProvider } from '@tanstack/react-query'

const createWrapper = () => {
  const queryClient = new QueryClient({
    defaultOptions: { queries: { retry: false } }
  })
  
  return ({ children }: { children: React.ReactNode }) => (
    <QueryClientProvider client={queryClient}>
      {children}
    </QueryClientProvider>
  )
}

describe('usePlayerData', () => {
  it('fetches player data successfully', async () => {
    const { result } = renderHook(() => usePlayerData('player-1'), {
      wrapper: createWrapper()
    })

    await waitFor(() => {
      expect(result.current.isSuccess).toBe(true)
    })

    expect(result.current.data).toBeDefined()
  })
})
```

---

## **10. Accessibility Guidelines**

### **10.1 Semantic HTML**
```typescript
// Good: Semantic markup
export const PlayerProfile = ({ player }: { player: Player }) => (
  <article className="player-profile">
    <header>
      <h1>{player.name}</h1>
      <p>Position: {player.position}</p>
    </header>
    
    <section aria-labelledby="stats-heading">
      <h2 id="stats-heading">Player Statistics</h2>
      <dl>
        <dt>Overall Rating</dt>
        <dd>{player.overall}</dd>
        <dt>Goals</dt>
        <dd>{player.stats.goals}</dd>
      </dl>
    </section>
  </article>
)
```

### **10.2 ARIA Attributes**
```typescript
// Interactive elements with proper ARIA
export const SearchFilter = ({ onFilter }: { onFilter: (term: string) => void }) => {
  const [searchTerm, setSearchTerm] = useState('')
  
  return (
    <div role="search">
      <label htmlFor="player-search" className="sr-only">
        Search players
      </label>
      <input
        id="player-search"
        type="text"
        value={searchTerm}
        onChange={(e) => setSearchTerm(e.target.value)}
        placeholder="Search players..."
        aria-describedby="search-help"
      />
      <div id="search-help" className="sr-only">
        Search by player name, position, or club
      </div>
      <button
        onClick={() => onFilter(searchTerm)}
        aria-label="Execute search"
      >
        Search
      </button>
    </div>
  )
}
```

### **10.3 Keyboard Navigation**
```typescript
// Keyboard navigation support
export const PlayerGrid = ({ players }: { players: Player[] }) => {
  const [focusedIndex, setFocusedIndex] = useState(0)
  
  const handleKeyDown = (e: KeyboardEvent) => {
    switch (e.key) {
      case 'ArrowRight':
        setFocusedIndex((prev) => Math.min(prev + 1, players.length - 1))
        break
      case 'ArrowLeft':
        setFocusedIndex((prev) => Math.max(prev - 1, 0))
        break
      case 'Enter':
      case ' ':
        // Handle selection
        break
    }
  }
  
  return (
    <div
      role="grid"
      onKeyDown={handleKeyDown}
      className="player-grid"
    >
      {players.map((player, index) => (
        <div
          key={player.id}
          role="gridcell"
          tabIndex={index === focusedIndex ? 0 : -1}
          aria-selected={index === focusedIndex}
        >
          <PlayerCard player={player} />
        </div>
      ))}
    </div>
  )
}
```

---

## **11. Code Quality Standards**

### **11.1 ESLint Configuration**
```json
{
  "extends": [
    "@typescript-eslint/recommended",
    "plugin:react/recommended",
    "plugin:react-hooks/recommended",
    "plugin:jsx-a11y/recommended"
  ],
  "rules": {
    "react/react-in-jsx-scope": "off",
    "react/prop-types": "off",
    "@typescript-eslint/no-unused-vars": "error",
    "@typescript-eslint/explicit-function-return-type": "warn",
    "jsx-a11y/no-autofocus": "warn",
    "prefer-const": "error",
    "no-var": "error"
  }
}
```

### **11.2 Prettier Configuration**
```json
{
  "semi": false,
  "singleQuote": true,
  "trailingComma": "es5",
  "tabWidth": 2,
  "printWidth": 80,
  "bracketSpacing": true,
  "arrowParens": "avoid"
}
```

### **11.3 Git Hooks**
```json
{
  "husky": {
    "hooks": {
      "pre-commit": "lint-staged",
      "commit-msg": "commitlint -E HUSKY_GIT_PARAMS"
    }
  },
  "lint-staged": {
    "*.{ts,tsx}": [
      "eslint --fix",
      "prettier --write"
    ]
  }
}
```

---

## **12. Documentation Standards**

### **12.1 Component Documentation**
```typescript
/**
 * PlayerCard component displays player information in a card format
 * 
 * @param player - Player object containing all player data
 * @param onClick - Optional callback when card is clicked
 * @param className - Additional CSS classes
 * @param showStats - Whether to display detailed statistics
 * 
 * @example
 * ```tsx
 * <PlayerCard
 *   player={playerData}
 *   onClick={(player) => navigate(`/players/${player.id}`)}
 *   showStats={true}
 * />
 * ```
 */
export interface PlayerCardProps {
  player: Player
  onClick?: (player: Player) => void
  className?: string
  showStats?: boolean
}

export const PlayerCard: React.FC<PlayerCardProps> = ({
  player,
  onClick,
  className = '',
  showStats = false
}) => {
  // Component implementation
}
```

### **12.2 Hook Documentation**
```typescript
/**
 * Custom hook for managing player data with caching and error handling
 * 
 * @param playerId - Unique identifier for the player
 * @param options - Optional configuration for the query
 * @returns Object containing player data, loading state, and error state
 * 
 * @example
 * ```tsx
 * const { data: player, isLoading, error } = usePlayerData('player-123', {
 *   refetchInterval: 30000 // Refetch every 30 seconds
 * })
 * ```
 */
export const usePlayerData = (
  playerId: string,
  options?: UseQueryOptions
) => {
  // Hook implementation
}
```

---

**Document Maintenance:**
- Guidelines Version: 1.0
- Last Updated: June 14, 2025
- Next Review: September 14, 2025
- Maintained By: Frontend Development Team