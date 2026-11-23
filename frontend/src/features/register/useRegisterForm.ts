import { useState } from 'react'
import type { SubmitEvent } from 'react'
import { ApiError, formatParameterErrors, formatRequestError } from '../../shared/lib/api'
import { useAuth } from '../../shared/lib/auth'
import type { User } from '../../shared/types'

type RegisterFormValues = {
  name: string
  email: string
  password: string
}

const emptyValues: RegisterFormValues = { name: '', email: '', password: '' }

export function useRegisterForm(onSuccess: (user: User) => void) {
  const { register } = useAuth()
  const [values, setValues] = useState<RegisterFormValues>(emptyValues)
  const [errors, setErrors] = useState<Record<string, string>>({})
  const [formError, setFormError] = useState('')
  const [isSubmitting, setIsSubmitting] = useState(false)

  const setValue = (name: string, value: string) => {
    setValues((previous) => ({ ...previous, [name]: value }))
    setErrors((previous) => {
      const next = { ...previous }
      delete next[name]
      return next
    })
  }

  const submit = async (event: SubmitEvent<HTMLFormElement>) => {
    event.preventDefault()
    setErrors({})
    setFormError('')
    setIsSubmitting(true)

    try {
      const user = await register(values.name, values.email, values.password)
      onSuccess(user)
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
