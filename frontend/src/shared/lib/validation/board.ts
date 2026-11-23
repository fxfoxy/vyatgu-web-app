import { validateRequiredText } from './text'

const BOARD_TITLE_RULES = {
  maxLength: 255,
  requiredMessage: 'Введите название доски',
  maxLengthMessage: 'Название не должно быть длиннее 255 символов',
}

export function validateBoardTitle(value: string): string {
  return validateRequiredText(value, BOARD_TITLE_RULES)
}
