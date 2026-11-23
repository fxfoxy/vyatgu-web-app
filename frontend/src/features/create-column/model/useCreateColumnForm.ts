import { useState } from 'react'
import type { SubmitEvent } from 'react'
import { ApiError, formatParameterErrors, formatRequestError } from '../../../shared/lib/api'
import type { BoardColumn } from '../../../shared/types'
import { createColumn } from '../api'

type CreateColumnFormValues = {
  title: string
  color: string | null
}

const emptyValues: CreateColumnFormValues = { title: '', color: null }

type CreateColumnOptions = {
  boardId: number
  ordinalPosition: number
  onCreated: (column: BoardColumn) => void
}

export function useCreateColumnForm({ boardId, ordinalPosition, onCreated }: CreateColumnOptions) {
  const [values, setValues] = useState<CreateColumnFormValues>(emptyValues)
  const [errors, setErrors] = useState<Record<string, string>>({})
  const [formError, setFormError] = useState('')
  const [isSubmitting, setIsSubmitting] = useState(false)

  const setTitle = (value: string) => {
    setValues((previous) => ({ ...previous, title: value }))
    setErrors((previous) => {
      const next = { ...previous }
      delete next.title
      return next
    })
  }

  const setColor = (value: string | null) => {
    setValues((previous) => ({ ...previous, color: value }))
    setErrors((previous) => {
      const next = { ...previous }
      delete next.color
      return next
    })
  }

  const submit = async (event: SubmitEvent<HTMLFormElement>) => {
    event.preventDefault()
    setFormError('')

    const title = values.title.trim()
    let titleError = ''

    if (!title) {
      titleError = 'Введите название колонки'
    } else if ([...title].length > 255) {
      titleError = 'Название не должно быть длиннее 255 символов'
    }

    if (titleError) {
      setErrors({ title: titleError })
      return
    }

    setErrors({})
    setIsSubmitting(true)

    try {
      const column = await createColumn({
        board_id: boardId,
        ordinal_position: ordinalPosition,
        title,
        color: values.color,
      })
      onCreated(column)
    } catch (error) {
      if (error instanceof ApiError) {
        const fieldErrors = formatParameterErrors(error.parameterErrors)
        setErrors(fieldErrors)
        // Ошибки полей показываем под полями, иначе — общий баннер
        setFormError(Object.keys(fieldErrors).length > 0 ? '' : formatRequestError(error))
      } else {
        setFormError('Не удалось выполнить запрос. Попробуйте ещё раз')
      }
    } finally {
      setIsSubmitting(false)
    }
  }

  return { values, errors, formError, isSubmitting, setTitle, setColor, submit }
}
