import { useCallback, useEffect, useState } from 'react'
import { ApiError, formatRequestError } from '../../../shared/lib/api'
import type { Board } from '../../../shared/types'
import { getBoards } from '../api'

export function useBoards() {
  const [boards, setBoards] = useState<Board[]>([])
  const [isLoading, setIsLoading] = useState(true)
  const [error, setError] = useState('')
  const [reloadToken, setReloadToken] = useState(0)

  useEffect(() => {
    let isCancelled = false

    getBoards()
      .then((nextBoards) => {
        if (!isCancelled) {
          setBoards(nextBoards)
        }
      })
      .catch((requestError: unknown) => {
        if (isCancelled) {
          return
        }

        setError(
          requestError instanceof ApiError
            ? formatRequestError(requestError)
            : 'Не удалось загрузить доски. Попробуйте ещё раз',
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
  }, [reloadToken])

  const reload = useCallback(() => {
    setIsLoading(true)
    setError('')
    setReloadToken((token) => token + 1)
  }, [])

  const addBoard = useCallback((board: Board) => {
    setBoards((previous) => [board, ...previous])
  }, [])

  const updateBoard = useCallback((board: Board) => {
    setBoards((previous) =>
      previous.map((item) => (item.board_id === board.board_id ? board : item)),
    )
  }, [])

  return { boards, isLoading, error, reload, addBoard, updateBoard }
}
