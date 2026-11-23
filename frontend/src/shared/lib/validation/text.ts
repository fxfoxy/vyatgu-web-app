export type RequiredTextRules = {
  maxLength: number
  requiredMessage: string
  maxLengthMessage: string
}

/**
 * Проверяет обязательный текст: пустоту после trim и максимальную длину.
 * Возвращает текст ошибки или пустую строку.
 */
export function validateRequiredText(value: string, rules: RequiredTextRules): string {
  const trimmed = value.trim()

  if (!trimmed) {
    return rules.requiredMessage
  }

  if ([...trimmed].length > rules.maxLength) {
    return rules.maxLengthMessage
  }

  return ''
}
