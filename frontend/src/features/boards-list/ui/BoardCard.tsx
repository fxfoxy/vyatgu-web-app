import { Link } from 'react-router'
import type { Board } from '../../../shared/types'

const dateFormatter = new Intl.DateTimeFormat('ru-RU', {
  day: 'numeric',
  month: 'long',
  year: 'numeric',
})

function formatDate(value: string | null): string {
  if (!value) {
    return ''
  }

  const date = new Date(value)
  if (Number.isNaN(date.getTime())) {
    return ''
  }

  return dateFormatter.format(date)
}

type BoardCardProps = {
  board: Board
}

export function BoardCard({ board }: BoardCardProps) {
  const updatedAt = formatDate(board.updated_datetime)
  const createdAt = formatDate(board.created_datetime)
  const dateLabel = updatedAt ? `Обновлена ${updatedAt}` : createdAt ? `Создана ${createdAt}` : ''

  return (
    <Link
      to={`/boards/${board.board_id}`}
      className="flex h-full flex-col gap-3 rounded-lg border border-slate-200 bg-white p-4 shadow-sm transition hover:border-indigo-300 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
    >
      <h3 className="text-lg font-semibold break-words text-slate-900">{board.title}</h3>

      {dateLabel && <p className="mt-auto text-sm text-slate-500">{dateLabel}</p>}
    </Link>
  )
}
