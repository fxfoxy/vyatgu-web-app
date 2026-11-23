export function fieldError(field: string, message: string): Record<string, string> {
  if (!message) {
    return {}
  }

  return { [field]: message }
}
