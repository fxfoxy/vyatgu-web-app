import type { BoardColumn } from '../../../shared/types'
import { Alert, Button } from '../../../shared/ui'
import { ColumnCard } from './ColumnCard'

type ColumnsListProps = {
  columns: BoardColumn[]
  isLoading: boolean
  error: string
  canAddColumn: boolean
  onRetry: () => void
  onCreate: () => void
}

export function ColumnsList({
  columns,
  isLoading,
  error,
  canAddColumn,
  onRetry,
  onCreate,
}: ColumnsListProps) {
  if (isLoading) {
    return (
      <div className="flex gap-4 overflow-hidden" aria-busy="true">
        {Array.from({ length: 3 }, (_, index) => (
          <div
            key={index}
            className="h-40 w-72 shrink-0 animate-pulse rounded-lg border border-slate-200 bg-white"
          />
        ))}
      </div>
    )
  }

  if (error) {
    return (
      <div className="flex flex-col items-start gap-3">
        <Alert message={error} />
        <Button variant="secondary" onClick={onRetry}>
          Повторить
        </Button>
      </div>
    )
  }

  if (columns.length === 0) {
    return (
      <div className="flex flex-col items-center gap-3 rounded-lg border border-dashed border-slate-300 bg-white p-10 text-center">
        <h2 className="text-lg font-semibold text-slate-900">В доске пока нет колонок</h2>
        <p className="max-w-md text-slate-600">Добавьте первую колонку, чтобы начать работу.</p>
        <Button onClick={onCreate}>Добавить колонку</Button>
      </div>
    )
  }

  return (
    <div className="-mx-4 overflow-x-auto px-4 pb-2">
      <ul className="flex items-start gap-4">
        {columns.map((column) => (
          <li key={column.column_id} className="w-72 shrink-0">
            <ColumnCard column={column} />
          </li>
        ))}

        <li className="w-72 shrink-0">
          {canAddColumn ? (
            <button
              type="button"
              onClick={onCreate}
              className="flex w-full items-center justify-center gap-2 rounded-lg border border-dashed border-slate-300 bg-white/60 px-3 py-4 text-sm font-medium text-slate-600 transition hover:border-indigo-300 hover:text-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
            >
              <span aria-hidden>+</span> Добавить колонку
            </button>
          ) : (
            <p className="rounded-lg border border-dashed border-slate-300 bg-white/60 px-3 py-4 text-center text-sm text-slate-500">
              Достигнут лимит колонок (33)
            </p>
          )}
        </li>
      </ul>
    </div>
  )
}
