import { useState } from 'react'
import type { SubmitEvent } from 'react'
import { ApiError, formatParameterErrors, formatRequestError } from '../api'

export type BoardFormErrors = Record<string, string>

type BoardFormOptions<TValues extends Record<string, unknown>> = {
  initialValues: TValues
  validate: (values: TValues) => BoardFormErrors
  onSubmit: (values: TValues) => Promise<void>
}

/**
 * Общее состояние формы доски: значения, ошибки полей, баннер и отправка.
 * Ошибки валидации приходят из validate(), ошибки API раскладываются
 * на поля через parameter_errors, остальное уходит в общий баннер.
 */
export function useBoardForm<TValues extends Record<string, unknown>>({
  initialValues,
  validate,
  onSubmit,
}: BoardFormOptions<TValues>) {
  const [values, setValues] = useState<TValues>(initialValues)
  const [errors, setErrors] = useState<BoardFormErrors>({})
  const [formError, setFormError] = useState('')
  const [isSubmitting, setIsSubmitting] = useState(false)

  const setValue = <K extends keyof TValues>(name: K, value: TValues[K]) => {
    setValues((previous) => ({ ...previous, [name]: value }))
    setErrors((previous) => {
      const next = { ...previous }
      delete next[String(name)]
      return next
    })
  }

  const submit = async (event: SubmitEvent<HTMLFormElement>) => {
    event.preventDefault()
    setFormError('')

    const nextErrors = validate(values)
    if (Object.keys(nextErrors).length > 0) {
      setErrors(nextErrors)
      return
    }

    setErrors({})
    setIsSubmitting(true)

    try {
      await onSubmit(values)
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

  return { values, errors, formError, isSubmitting, setValue, submit }
}
