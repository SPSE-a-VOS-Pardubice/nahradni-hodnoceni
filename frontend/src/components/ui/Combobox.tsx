import * as React from "react"
import { Check, ChevronsUpDown } from "lucide-react"

import { cn } from "@/lib/utils"
import { Button } from "@/components/ui/button"
import {
  Command,
  CommandEmpty,
  CommandGroup,
  CommandInput,
  CommandItem,
} from "@/components/ui/command"
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from "@/components/ui/popover"

interface Props {
    selectTarget: string,
    data: string[]
}

function Combobox({ selectTarget, data }: Props) {
  const [open, setOpen] = React.useState(false)
  const [value, setValue] = React.useState("")

  return (
    <Popover open={open} onOpenChange={setOpen}>
      <PopoverTrigger asChild>
        <Button
          variant="default"
          role="combobox"
          aria-expanded={open}
          className="w-[200px] justify-between bg-[#9c9c9c] rounded-[2.5px] hover:bg-[#479cff] shadow-none"
        >
          {value
            ? data.find((item) => item.toLowerCase() === value)
            : `Vyberte ${selectTarget}...`}
          <ChevronsUpDown className="ml-2 h-4 w-4 shrink-0 opacity-50 stroke-white" />
        </Button>
      </PopoverTrigger>
      <PopoverContent className="w-[200px] p-0 border-[3px] border-[#479cff] rounded-[2.5px] shadow-none">
        <Command>
          <CommandInput placeholder={`Hledat ${selectTarget}...`} />
          <CommandEmpty>Žádné výsledky.</CommandEmpty>
          <CommandGroup>
            {data.map((item, i) => (
              <CommandItem
                key={i}
                value={item}
                onSelect={(currentValue) => {
                  setValue(currentValue === value ? "" : currentValue)
                  setOpen(false)
                }}
              >
                <Check
                  className={cn(
                    "mr-2 h-4 w-4",
                    value === item ? "opacity-100" : "opacity-0"
                  )}
                />
                {item}
              </CommandItem>
            ))}
          </CommandGroup>
        </Command>
      </PopoverContent>
    </Popover>
  )
}

export default Combobox;
