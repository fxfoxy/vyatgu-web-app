/**
 * Доска — в том виде, в каком её отдаёт бэкенд
 * (см. BoardResponseModel в app/src/Library/Api/Responses).
 */
export type Board = {
  board_id: number
  title: string
  owner_user_id: number
  is_public: boolean
  created_datetime: string
  updated_datetime: string | null
}
