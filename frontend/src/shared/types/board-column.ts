/**
 * Колонка доски — в том виде, в каком её отдаёт бэкенд
 * (см. BoardColumnResponseModel в app/src/Library/Api/Responses).
 */
export type BoardColumn = {
  column_id: number
  board_id: number
  /** Позиция колонки на доске, 0..32. */
  ordinal_position: number
  title: string
  /** 6 символов hex без '#': например '6366f1'. */
  color: string | null
}
