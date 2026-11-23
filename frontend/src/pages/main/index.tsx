import { useState } from 'react'
import { BoardsList, useBoards } from '../../features/boards-list'
import { CreateBoardModal } from '../../features/create-board'
import { useAuth } from '../../shared/lib/auth'
import { Button } from '../../shared/ui'

export function MainPage() {
  const { user } = useAuth()
  const { boards, isLoading, error, reload, addBoard } = useBoards()
  const [isCreating, setIsCreating] = useState(false)

  return (
    <div className="flex flex-col gap-6">
      <div className="flex flex-wrap items-end justify-between gap-4">
        <div>
          <h1 className="text-2xl font-semibold text-slate-900">Здравствуйте, {user?.name}</h1>
          <p className="mt-1 text-slate-600">Ваши доски</p>
        </div>

        {boards.length > 0 && <Button onClick={() => setIsCreating(true)}>Создать доску</Button>}
      </div>

      <BoardsList
        boards={boards}
        isLoading={isLoading}
        error={error}
        onRetry={reload}
        onCreate={() => setIsCreating(true)}
      />

      <CreateBoardModal
        isOpen={isCreating}
        onClose={() => setIsCreating(false)}
        onCreated={(board) => {
          addBoard(board)
          setIsCreating(false)
        }}
      />
    </div>
  )
}
