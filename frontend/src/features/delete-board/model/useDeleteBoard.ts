import { useState } from 'react'
import { ApiError, formatRequestError } from '../../../shared/lib/api'
import { deleteBoard } from '../api'

export function useDeleteBoard(boardId: number, onDeleted: () => void) {
  const [isSubmitting, setIsSubmitting] = useState(false)
  const [error, setError] = useState('')

  const submit = async () => {
    setError('')
    setIsSubmitting(true)

    try {
      await deleteBoard(boardId)
      onDeleted()
    } catch (requestError) {
      setError(
        requestError instanceof ApiError
          ? formatRequestError(requestError)
          : 'Не удалось удалить доску. Попробуйте ещё раз',
      )
    } finally {
      setIsSubmitting(false)
    }
  }

  return { isSubmitting, error, submit }
}
