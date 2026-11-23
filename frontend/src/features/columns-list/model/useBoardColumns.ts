import { useCallback, useEffect, useState } from 'react'
import { ApiError, formatRequestError } from '../../../shared/lib/api'
import type { BoardColumn } from '../../../shared/types'
import { getBoardColumns } from '../api'

/** Максимальная позиция колонки на бэкенде (0..32 → 33 колонки). */
const MAX_ORDINAL = 32

function byOrdinal(a: BoardColumn, b: BoardColumn): number {
  return a.ordinal_position - b.ordinal_position
}

function findFreeOrdinal(columns: BoardColumn[]): number | null {
  const used = new Set(columns.map((column) => column.ordinal_position))

  for (let position = 0; position <= MAX_ORDINAL; position += 1) {
    if (!used.has(position)) {
      return position
    }
  }

  return null
}

export function useBoardColumns(boardId: number) {
  const [columns, setColumns] = useState<BoardColumn[]>([])
  const [isLoading, setIsLoading] = useState(true)
  const [error, setError] = useState('')
  const [reloadToken, setReloadToken] = useState(0)

  useEffect(() => {
    let isCancelled = false

    getBoardColumns(boardId)
      .then((nextColumns) => {
        if (!isCancelled) {
          setColumns([...nextColumns].sort(byOrdinal))
        }
      })
      .catch((requestError: unknown) => {
        if (isCancelled) {
          return
        }

        setError(
          requestError instanceof ApiError
            ? formatRequestError(requestError)
            : 'Не удалось загрузить колонки. Попробуйте ещё раз',
        )
      })
      .finally(() => {
        if (!isCancelled) {
          setIsLoading(false)
        }
      })

    return () => {
      isCancelled = true
    }
  }, [boardId, reloadToken])

  const reload = useCallback(() => {
    setIsLoading(true)
    setError('')
    setReloadToken((token) => token + 1)
  }, [])

  const addColumn = useCallback((column: BoardColumn) => {
    setColumns((previous) => [...previous, column].sort(byOrdinal))
  }, [])

  const nextOrdinal = findFreeOrdinal(columns)

  return {
    columns,
    isLoading,
    error,
    reload,
    addColumn,
    nextOrdinal,
    canAddColumn: nextOrdinal !== null,
  }
}
