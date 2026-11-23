import { useId } from 'react'

type ColorSwitchPickerProps = {
  label?: string
  /** Цвет в формате 'rrggbb' без '#' или null. */
  value: string | null
  onChange: (value: string | null) => void
  error?: string
}

const COLORS = ['6366f1', '0ea5e9', '10b981', 'f59e0b', 'ef4444', 'ec4899', '8b5cf6'] as const

export function ColorSwitchPicker({
  label = 'Цвет',
  value,
  onChange,
  error,
}: ColorSwitchPickerProps) {
  const id = useId()
  const labelId = `${id}-label`
  const errorId = `${id}-error`

  return (
    <div className="flex flex-col gap-2">
      <span id={labelId} className="text-sm font-medium text-slate-700">
        {label}
      </span>

      <div
        role="group"
        aria-labelledby={labelId}
        aria-describedby={error ? errorId : undefined}
        className="flex flex-wrap items-center gap-2"
      >
        <button
          type="button"
          onClick={() => onChange(null)}
          aria-pressed={value === null}
          className={`rounded-full border px-3 py-1 text-xs font-medium transition ${
            value === null
              ? 'border-indigo-500 bg-indigo-50 text-indigo-700'
              : 'border-slate-300 bg-white text-slate-600 hover:border-slate-400'
          }`}
        >
          Без цвета
        </button>

        {COLORS.map((color) => (
          <button
            key={color}
            type="button"
            onClick={() => onChange(color)}
            aria-pressed={value === color}
            aria-label={`Цвет #${color}`}
            style={{ backgroundColor: `#${color}` }}
            className={`size-7 rounded-full border-2 transition ${
              value === color
                ? 'border-indigo-500 ring-2 ring-indigo-200'
                : 'border-white shadow-sm hover:scale-110'
            }`}
          />
        ))}
      </div>

      {error && (
        <p id={errorId} className="text-sm text-red-600">
          {error}
        </p>
      )}
    </div>
  )
}
