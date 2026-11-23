import type { BoardColumn } from '../../../shared/types'

type ColumnCardProps = {
  column: BoardColumn
}

export function ColumnCard({ column }: ColumnCardProps) {
  return (
    <article className="flex h-full flex-col gap-3 rounded-lg border border-slate-200 bg-slate-50 p-3">
      <header className="flex items-center gap-2">
        {column.color && (
          <span
            aria-hidden
            className="size-2.5 shrink-0 rounded-full"
            style={{ backgroundColor: `#${column.color}` }}
          />
        )}
        <h2 className="text-sm font-semibold break-words text-slate-900">{column.title}</h2>
      </header>

      <div className="grid min-h-24 place-items-center rounded-md border border-dashed border-slate-300 bg-white p-3 text-center text-sm text-slate-400">
        Карточек пока нет
      </div>
    </article>
  )
}
