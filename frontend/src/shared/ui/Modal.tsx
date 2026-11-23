import { useEffect, useId, useRef } from 'react'
import type { ReactNode } from 'react'

type ModalProps = {
  isOpen: boolean
  title: string
  onClose: () => void
  children: ReactNode
}

/**
 * Модальное окно на нативном <dialog>: браузер сам держит фокус внутри,
 * закрывает по Esc и возвращает фокус на элемент, который его открыл.
 */
export function Modal({ isOpen, title, onClose, children }: ModalProps) {
  const dialogRef = useRef<HTMLDialogElement>(null)
  const titleId = useId()

  useEffect(() => {
    const dialog = dialogRef.current
    if (!dialog) {
      return
    }

    if (isOpen && !dialog.open) {
      dialog.showModal()
      // showModal() фокусирует первый focusable-элемент, но React ставит
      // autoFocus свойством, а не атрибутом, поэтому фокусируем поле сами.
      const firstField = dialog.querySelector<HTMLElement>(
        '[data-modal-content] input:not([type="hidden"]), [data-modal-content] textarea, [data-modal-content] select',
      )
      firstField?.focus()
    } else if (!isOpen && dialog.open) {
      dialog.close()
    }
  }, [isOpen])

  useEffect(() => {
    if (!isOpen) {
      return
    }

    const previousOverflow = document.body.style.overflow
    document.body.style.overflow = 'hidden'

    return () => {
      document.body.style.overflow = previousOverflow
    }
  }, [isOpen])

  return (
    <dialog
      ref={dialogRef}
      aria-labelledby={titleId}
      onCancel={(event) => {
        event.preventDefault()
        onClose()
      }}
      onClick={(event) => {
        if (event.target === event.currentTarget) {
          onClose()
        }
      }}
      className="m-auto w-[calc(100%-2rem)] max-w-md rounded-lg border border-slate-200 bg-white p-0 shadow-xl backdrop:bg-slate-900/40"
    >
      <div className="flex items-center justify-between gap-4 border-b border-slate-200 px-5 py-4">
        <h2 id={titleId} className="text-lg font-semibold text-slate-900">
          {title}
        </h2>

        <button
          type="button"
          onClick={onClose}
          aria-label="Закрыть"
          className="grid size-8 place-items-center rounded-md text-slate-500 transition hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
        >
          ×
        </button>
      </div>

      <div data-modal-content className="px-5 py-4">
        {children}
      </div>
    </dialog>
  )
}
