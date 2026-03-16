<script setup lang="ts">
import { ref, watch } from 'vue'
import axios from 'axios'


import {
  Dialog,
  DialogTrigger,
  DialogContent,
  DialogDescription,
} from '@/components/ui/dialog_create'


import {
  Stepper,
  StepperDescription,
  StepperItem,
  StepperSeparator,
  StepperTitle,
  StepperTrigger,
} from '@/components/ui/stepper'

import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { Calendar } from '@/components/ui/calendar'
import { AlertTriangle, CalendarIcon, FileText, HandCoins, Home } from 'lucide-vue-next'
import { Popover, PopoverTrigger, PopoverContent } from '@/components/ui/popover'
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem } from '@/components/ui/select'
import { DateFormatter, DateValue, getLocalTimeZone } from '@internationalized/date'
import { cn } from '@/lib/utils'
import { Form, FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form'
import * as z from 'zod'
import { toTypedSchema } from '@vee-validate/zod'
import { toast } from 'vue-sonner'
import Swal from 'sweetalert2'


const emit = defineEmits(['saved'])
const popoverOpen = ref(false)
const open = ref(false)

// Reactive refs
const province = ref('')
const municipality = ref('')
const barangay = ref('')

const municipalities = ref([])
const barangays = ref([])

// Watch province and fetch municipalities
watch(province, async (val) => {
  municipality.value = ''
  barangays.value = []
  barangay.value = ''
  if (!val) {
    municipalities.value = []
    return
  }
  const res = await axios.get(`/api/municipalities/${val}`)
  municipalities.value = res.data
})

// Watch municipality and fetch barangays
watch(municipality, async (val) => {
  barangay.value = ''
  if (!val) {
    barangays.value = []
    return
  }
  const res = await axios.get(`/api/barangays/${val}`)
  barangays.value = res.data
})

const formSchema = [
  z.object({
    IncidentType: z.enum([
      "Fire Incident",
      "Tornado Incident",
      "Vehicular Incident",
      "Explosion Incident",
      "Landslide",
      "Flashflood",
      "Armed Conflict",
    ]),
    IncidentDate: z.any()
    .refine(val => val?.toDate instanceof Function)
    .transform(val => val.toDate(getLocalTimeZone())),
    IncidentTime: z
      .string()
      .regex(/^([01]\d|2[0-3]):([0-5]\d)$/, "Invalid time"),
    IncidentProvince: z.string().min(1, "Province is required"),
    IncidentMunicipality: z.string().min(1, "Municipality is required"),
    IncidentBarangay: z.string().min(1, "Barangay is required"),
  }),

  z.object({
    IncidentOverview: z.string().min(1, "Situationer is required")
  }),


   z.object({
    affectedFamilies: z.coerce.number().min(0).default(0),
    affectedIndividual: z.coerce.number().min(0).default(0),
    partiallydamaged: z.coerce.number().min(0).default(0),
    totallydamaged: z.coerce.number().min(0).default(0),
    }).refine(
    (data) => data.affectedFamilies <= data.affectedIndividual,
    {
        message: "Affected families cannot be greater than affected individuals",
        path: ["affectedFamilies"],
    }
    ),

    z.object({
    openec: z.enum(["true", "false"]),

    displacedfamily: z.coerce
        .number()
        .min(0)
        .optional(),

    displacedindividual: z.coerce
        .number()
        .min(0)
        .optional(),
    })
    .superRefine((data, ctx) => {
    if (data.openec === "true") {

        if (data.displacedfamily === undefined || data.displacedfamily === null) {
        ctx.addIssue({
            path: ["displacedfamily"],
            code: z.ZodIssueCode.custom,
            message: "Number of displaced families is required",
        })
        }

        if (data.displacedindividual === undefined || data.displacedindividual === null) {
        ctx.addIssue({
            path: ["displacedindividual"],
            code: z.ZodIssueCode.custom,
            message: "Number of displaced individuals is required",
        })
        }

    }
    })
]

const stepIndex = ref(1)
const steps = [
  {
    step: 1,
    title: 'Incident Info',
    description: 'Incident Type',
    icon: AlertTriangle
  },
  {
    step: 2,
    title: 'Situationer & Photos',
    description: 'Situation & Damage',
    icon: FileText
  },
  {
    step: 3,
    title: 'Affected and Damaged Houses',
    description: 'Affected and Damaged Houses',
    icon: Home
  },
 {
    step: 4,
    title: 'Cost of Assistance',
    description: 'Cost of Assistance',
    icon: HandCoins
  },
]


const df = new DateFormatter('en-US', { dateStyle: 'long' })


function formatCurrency(value: any) {
  if (!value) return ''
  return Number(value).toLocaleString('en-PH', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  })
}

function parseCurrency(value: string) {
  return value.replace(/,/g, '')
}

function formatDateForDB(dateValue: DateValue) {
  if (!dateValue) return null
  const jsDate = dateValue.toDate(getLocalTimeZone())
  return jsDate.toISOString().slice(0, 10) // '2026-03-11'
}

async function onSubmit(values: any) {
  const payload = {
    incident_type: values.IncidentType,
    incident_date: formatDateForDB(values.IncidentDate),
    incident_time: values.IncidentTime,
    province: values.IncidentProvince,
    municipality: values.IncidentMunicipality,
    barangay: values.IncidentBarangay,
    overview: values.IncidentOverview,
    affected_families: Number(values.affectedFamilies || 0),
    affected_individuals: Number(values.affectedIndividual || 0),
    partially_damaged: Number(values.partiallydamaged || 0),
    totally_damaged: Number(values.totallydamaged || 0),
    open_ec: values.openec === 'true' ? 1 : 0,
    displaced_family: Number(values.displacedfamily || 0),
    displaced_individual: Number(values.displacedindividual || 0),
    dswd_cost: Number(values.dswdcost || 0),
    lgu_cost: Number(values.lgucost || 0),
    ngo_cost: Number(values.ngocost || 0),
    partners_cost: Number(values.partners || 0),
  }

  console.log(payload)


  try {
    await axios.post('/api/minor_incident/sitrep', payload)

    await Swal.fire({
        toast: true,
        position: 'top',
        icon: 'success',
        title: 'Sitrep saved',
        text: 'The situational report has been successfully created.',
        showConfirmButton: false,
        timer: 2000
        })
   open.value = false
    emit('saved') // refresh parent table
  } catch (error) {

    Swal.fire({
      icon: 'error',
      title: 'Save Failed',
      text: 'Something went wrong while saving the sitrep.'
    })

    console.error(error)
  }
}

</script>

<template>
  <Dialog v-model:open="open">
    <DialogTrigger>
      <Button>Create Sitrep</Button>
    </DialogTrigger>

    <DialogContent>
        <DialogDescription> Create Situational Report </DialogDescription>
        <div class="flex items-start justify-center mt-10">
            <Form
                v-slot="{ meta, values, validate }"
                as="" keep-values :validation-schema="toTypedSchema(formSchema[stepIndex - 1])"
            >
                <Stepper v-slot="{ isNextDisabled, isPrevDisabled, nextStep, prevStep, modelValue }" v-model="stepIndex" class="block w-full">
                <form
                    @submit="(e) => {
                    e.preventDefault()
                    validate()
                    if (stepIndex === steps.length && meta.valid) {
                        onSubmit(values)
                    }
                    }"
                >
                    <div class="flex w-full flex-start gap-2">
                        <StepperItem
                            v-for="(step, index) in steps"
                            :key="step.step"
                            v-slot="{ state }"
                            class="relative flex w-full flex-col items-center justify-center"
                            :step="step.step"
                        >
                            <StepperSeparator
                            v-if="step.step !== steps[steps.length - 1].step"
                            class="absolute left-[calc(50%+20px)] right-[calc(-50%+10px)] top-5 block h-0.5 shrink-0 rounded-full bg-muted group-data-[state=completed]:bg-primary"
                            />
                            <StepperTrigger as-child>
                            <Button
                                :variant="state === 'completed' || state === 'active' ? 'default' : 'outline'"
                                size="icon"
                                class="z-10 rounded-full shrink-0"
                                :class="[state === 'active' && 'ring-2 ring-ring ring-offset-2 ring-offset-background']"
                                :disabled="state !== 'completed' && (index >= (modelValue || 0) && !meta.valid)"
                            >
                               <component
                                :is="step.icon"
                                class="size-5"
                                />
                            </Button>
                            </StepperTrigger>
                            <div class="mt-5 flex flex-col items-center text-center">
                            <StepperTitle
                                :class="[state === 'active' && 'text-primary']"
                                class="text-sm font-semibold transition lg:text-base"
                            >
                                {{ step.title }}
                            </StepperTitle>
                            <StepperDescription
                                :class="[state === 'active' && 'text-primary']"
                                class="sr-only text-xs text-muted-foreground transition md:not-sr-only lg:text-sm"
                            >
                                {{ step.description }}
                            </StepperDescription>
                            </div>
                        </StepperItem>
                    </div>


                        <template v-if="stepIndex === 1">
                            <h1 class="border-b-2 border-red-500 pb-2 font-semibold mt-10 text-center">
                            Incident Information
                            </h1>
                            <div class="grid grid-cols-3 gap-5 py-3 mt-10">
                                <div class="flex flex-col space-y-3">
                                    <FormField v-slot="{ componentField }" name="IncidentType">
                                    <FormItem>
                                        <FormLabel>Incident Type</FormLabel>
                                        <FormControl>
                                           <Select
                                            :model-value="componentField.modelValue"
                                            @update:model-value="val => componentField.onChange(val)"
                                            >
                                                <SelectTrigger>
                                                    <SelectValue placeholder="Select" /> </SelectTrigger>
                                                    <SelectContent position="popper">
                                                        <SelectItem value="Fire Incident">Fire Incident</SelectItem>
                                                        <SelectItem value="Tornado Incident">Tornado Incident</SelectItem>
                                                        <SelectItem value="Vehicular Incident">Vehicular Incident</SelectItem>
                                                        <SelectItem value="Explosion Incident">Explosion Incident</SelectItem>
                                                        <SelectItem value="Landslide">Landslide</SelectItem>
                                                        <SelectItem value="Flashflood">Flashflood</SelectItem>
                                                        <SelectItem value="Armed Conflict">Armed Conflict</SelectItem>
                                                    </SelectContent>
                                            </Select>
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                    </FormField>
                                </div>

                                <div class="flex flex-col w-full space-y-3">
                                    <FormField v-slot="{ componentField }" name="IncidentDate">
                                    <FormItem>
                                        <FormLabel>Date of Incidents</FormLabel>
                                       <Popover v-model:open="popoverOpen">
                                        <PopoverTrigger as-child>
                                            <Button
                                            variant="outline"
                                            :class="cn(
                                                'w-[280px] justify-start text-left font-normal',
                                                !componentField.modelValue && 'text-muted-foreground'
                                            )"
                                            >
                                            <CalendarIcon class="mr-2 h-4 w-4" />
                                            {{ componentField.modelValue
                                                ? df.format(componentField.modelValue.toDate(getLocalTimeZone()))
                                                : "Pick a date"
                                            }}
                                            </Button>
                                        </PopoverTrigger>

                                        <PopoverContent class="w-auto p-0">
                                            <Calendar
                                            :model-value="componentField.modelValue"
                                            @update:model-value="(val) => {
                                                componentField.onChange(val);
                                                popoverOpen = false;
                                            }"
                                            layout="month-and-year"
                                            initial-focus
                                            />
                                        </PopoverContent>
                                        </Popover>
                                        <FormMessage />
                                    </FormItem>
                                    </FormField>
                                </div>

                                <div class="flex flex-col w-full space-y-3">
                                    <FormField v-slot="{ componentField }" name="IncidentTime">
                                    <FormItem>
                                        <FormLabel>TIme of Incident</FormLabel>
                                        <FormControl>
                                        <Input type="time"  v-bind="componentField" />
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                    </FormField>
                                </div>


                                <div class="flex flex-col space-y-3">
                                    <FormField v-slot="{ componentField }" name="IncidentProvince">
                                    <FormItem>
                                        <FormLabel>Province</FormLabel>
                                        <FormControl>
                                                <Select
                                                        :model-value="province"
                                                        @update:model-value="val => { province = val; componentField.onChange(val) }"
                                                    >
                                                <SelectTrigger id="province">
                                                    <SelectValue placeholder="Select" /> </SelectTrigger>
                                                    <SelectContent position="popper">
                                                        <SelectItem value="Aurora">Aurora</SelectItem>
                                                        <SelectItem value="Bataan">Bataan</SelectItem>
                                                        <SelectItem value="Bulacan">Bulacan</SelectItem>
                                                        <SelectItem value="Nueva Ecija">Nueva Ecija</SelectItem>
                                                        <SelectItem value="Pampanga">Pampanga</SelectItem>
                                                        <SelectItem value="Tarlac">Tarlac</SelectItem>
                                                        <SelectItem value="Zambales">Zambales</SelectItem>
                                                    </SelectContent>
                                            </Select>
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                    </FormField>
                                </div>

                                <div class="flex flex-col space-y-3">
                                    <FormField v-slot="{ componentField }" name="IncidentMunicipality">
                                    <FormItem>
                                        <FormLabel>Municipality</FormLabel>
                                        <FormControl>
                                            <Select
                                                :model-value="municipality"
                                                @update:model-value="val => { municipality = val; componentField.onChange(val) }"
                                            >
                                                <SelectTrigger id="municipality">
                                                    <SelectValue placeholder="Select" /> </SelectTrigger>
                                                    <SelectContent position="popper">
                                                    <SelectItem v-for="m in municipalities" :key="m" :value="m">{{ m }}</SelectItem>
                                                    </SelectContent>
                                            </Select>
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                    </FormField>
                                </div>

                                <div class="flex flex-col space-y-3">
                                    <FormField v-slot="{ componentField }" name="IncidentBarangay">
                                    <FormItem>
                                        <FormLabel>Barangay</FormLabel>
                                        <FormControl>
                                            <Select
                                                :model-value="barangay"
                                                @update:model-value="val => { barangay = val; componentField.onChange(val) }"
                                            >
                                                <SelectTrigger id="barangay">
                                                    <SelectValue placeholder="Select" /> </SelectTrigger>
                                                    <SelectContent position="popper">
                                                    <SelectItem v-for="b in barangays" :key="b" :value="b">{{ b }}</SelectItem>
                                                    </SelectContent>
                                            </Select>
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                    </FormField>
                                </div>
                            </div>
                                </template>


                            <template v-if="stepIndex === 2">
                                <h1 class="border-b-2 border-red-500 pb-2 font-semibold mt-10 mb-8 text-center">
                                Situation Overview and Photos
                                </h1>
                                <FormField v-slot="{ componentField }" name="IncidentOverview">
                                <FormItem>
                                    <FormLabel>Situationer</FormLabel>
                                    <FormControl>
                                    <Textarea placeholder="Input your situationer...." v-bind="componentField" rows="8" class="resize-y text-justify"/>
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                                </FormField>
                            </template>



                            <template v-if="stepIndex === 3">
                            <h1 class="border-b-2 border-red-500 pb-2 font-semibold mt-10 mb-7 text-center">
                            Status of Affected, Displaced Families and Damaged Houses
                            </h1>
                            <div class="grid grid-cols-2 gap-5 py-3">
                                <div class="flex flex-col space-y-3">
                                <FormField v-slot="{ componentField }" name="affectedfamilies">
                                <FormItem>
                                    <FormLabel>Number of Affected Families</FormLabel>
                                    <FormControl>
                                    <Input type="number" v-bind="componentField" />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                                </FormField>
                                </div>

                                <div class="flex flex-col space-y-3">
                                <FormField v-slot="{ componentField }" name="affectedindividuals">
                                <FormItem>
                                    <FormLabel>Number of Affected Individuals</FormLabel>
                                    <FormControl>
                                    <Input type="number" v-bind="componentField" />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                                </FormField>
                                </div>

                                <div class="flex flex-col space-y-3">
                                <FormField v-slot="{ componentField }" name="partiallydamaged">
                                <FormItem>
                                    <FormLabel>Number of Partially Damaged Houses</FormLabel>
                                    <FormControl>
                                    <Input type="number" v-bind="componentField" />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                                </FormField>
                                </div>

                                <div class="flex flex-col space-y-3">
                                <FormField v-slot="{ componentField }" name="totallydamaged">
                                <FormItem>
                                    <FormLabel>Number of Totally Damaged Houses</FormLabel>
                                    <FormControl>
                                    <Input type="number" v-bind="componentField" />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                                </FormField>
                                </div>
                            </div>

                            <div class="grid grid-cols-4 gap-5 py-3">
                                <div class="flex flex-col-2 space-y-3">
                                    <FormField v-slot="{ componentField }" name="openec">
                                    <FormItem>
                                        <FormLabel>Open Evacuation Center?</FormLabel>
                                        <FormControl>
                                        <Select
                                            :model-value="componentField.modelValue"
                                            @update:model-value="val => componentField.onChange(val)"
                                            >
                                                <SelectTrigger id="openec">
                                                    <SelectValue placeholder="Select" /> </SelectTrigger>
                                                    <SelectContent position="popper">
                                                        <SelectItem value="true">Yes</SelectItem>
                                                        <SelectItem value="false">No</SelectItem>
                                                    </SelectContent>
                                            </Select>
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                    </FormField>
                                </div>

                                <template v-if="values.openec === 'true'">
                                <div class="flex flex-col space-y-3">
                                    <FormField v-slot="{ componentField }" name="displacedfamily">
                                    <FormItem>
                                        <FormLabel>Number of Displaced Families</FormLabel>
                                        <FormControl>
                                        <Input type="number" v-bind="componentField" />
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                    </FormField>
                                </div>

                                <div class="flex flex-col space-y-3">
                                    <FormField v-slot="{ componentField }" name="displacedindividual">
                                    <FormItem>
                                        <FormLabel>Number of Displaced Individuals</FormLabel>
                                        <FormControl>
                                        <Input type="number" v-bind="componentField" />
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                    </FormField>
                                </div>
                                </template>
                            </div>

                            <div class="grid grid-cols-4 gap-5 py-3">
                                <div class="flex flex-col-2 space-y-3">
                                    <FormField v-slot="{ componentField }" name="casualties">
                                    <FormItem>
                                        <FormLabel>Casualties?</FormLabel>
                                        <FormControl>
                                        <Select
                                            :model-value="componentField.modelValue"
                                            @update:model-value="val => componentField.onChange(val)"
                                            >
                                                <SelectTrigger id="casualties">
                                                    <SelectValue placeholder="Select" /> </SelectTrigger>
                                                    <SelectContent position="popper">
                                                        <SelectItem value="true">Yes</SelectItem>
                                                        <SelectItem value="false">No</SelectItem>
                                                    </SelectContent>
                                            </Select>
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                    </FormField>
                                </div>

                                <template v-if="values.casualties === 'true'">
                                <div class="flex flex-col space-y-3">
                                    <FormField v-slot="{ componentField }" name="dead">
                                    <FormItem>
                                        <FormLabel>Dead</FormLabel>
                                        <FormControl>
                                        <Input type="number" v-bind="componentField" />
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                    </FormField>
                                </div>

                                <div class="flex flex-col space-y-3">
                                    <FormField v-slot="{ componentField }" name="injured">
                                    <FormItem>
                                        <FormLabel>Injured</FormLabel>
                                        <FormControl>
                                        <Input type="number" v-bind="componentField" />
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                    </FormField>
                                </div>

                                <div class="flex flex-col space-y-3">
                                    <FormField v-slot="{ componentField }" name="missing">
                                    <FormItem>
                                        <FormLabel>Missing</FormLabel>
                                        <FormControl>
                                        <Input type="number" v-bind="componentField" />
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                    </FormField>
                                </div>
                                </template>
                            </div>

                        </template>


                        <template v-if="stepIndex === 4">
                            <h1 class="border-b-2 border-red-500 pb-2 font-semibold mt-10 mb-7 text-center">
                            Cost of Humanitarian Assistance Provided
                            </h1>
                            <div class="grid grid-cols-4 gap-5 py-3">
                                <div class="flex flex-col space-y-3">
                                    <FormField v-slot="{ componentField }" name="dswdcost">
                                    <FormItem>
                                        <FormLabel>DSWD</FormLabel>
                                         <FormControl>
                                            <div class="relative">
                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground">
                                                ₱
                                            </span>

                                            <Input
                                                type="text"
                                                inputmode="decimal"
                                                :model-value="formatCurrency(componentField.modelValue)"
                                                @input="e => componentField.onChange(parseCurrency(e.target.value))"
                                                class="pl-8"
                                                placeholder="0.00"
                                            />
                                            </div>
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                    </FormField>
                                </div>

                                <div class="flex flex-col space-y-3">
                                    <FormField v-slot="{ componentField }" name="lgucost">
                                    <FormItem>
                                        <FormLabel>LGU</FormLabel>
                                         <FormControl>
                                            <div class="relative">
                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground">
                                                ₱
                                            </span>

                                            <Input
                                                type="text"
                                                inputmode="decimal"
                                                :model-value="formatCurrency(componentField.modelValue)"
                                                @input="e => componentField.onChange(parseCurrency(e.target.value))"
                                                class="pl-8"
                                                placeholder="0.00"
                                            />
                                            </div>
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                    </FormField>
                                </div>

                                <div class="flex flex-col space-y-3">
                                    <FormField v-slot="{ componentField }" name="ngocost">
                                    <FormItem>
                                        <FormLabel>NGOs</FormLabel>
                                         <FormControl>
                                            <div class="relative">
                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground">
                                                ₱
                                            </span>

                                            <Input
                                                type="text"
                                                inputmode="decimal"
                                                :model-value="formatCurrency(componentField.modelValue)"
                                                @input="e => componentField.onChange(parseCurrency(e.target.value))"
                                                class="pl-8"
                                                placeholder="0.00"
                                            />
                                            </div>
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                    </FormField>
                                </div>

                                <div class="flex flex-col space-y-3">
                                    <FormField v-slot="{ componentField }" name="partners">
                                    <FormItem>
                                        <FormLabel>Other Partners</FormLabel>
                                        <FormControl>
                                            <div class="relative">
                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground">
                                                ₱
                                            </span>

                                            <Input
                                                type="text"
                                                inputmode="decimal"
                                                :model-value="formatCurrency(componentField.modelValue)"
                                                @input="e => componentField.onChange(parseCurrency(e.target.value))"
                                                class="pl-8"
                                                placeholder="0.00"
                                            />
                                            </div>
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                    </FormField>
                                </div>
                            </div>

                        </template>

                        <div class="flex items-center justify-between mt-4">
                            <Button :disabled="isPrevDisabled" variant="outline" size="sm" @click="prevStep()">
                                Back
                            </Button>
                            <div class="flex items-center gap-3">
                                <Button v-if="stepIndex !== 4" :type="meta.valid ? 'button' : 'submit'" :disabled="isNextDisabled" size="sm" @click="meta.valid && nextStep()">
                                Next
                                </Button>
                                <Button
                                v-if="stepIndex === 4" size="sm" type="submit"
                                >
                                Create Sitrep
                                </Button>
                            </div>
                        </div>
                </form>
                </Stepper>
            </Form>
            </div>
        </DialogContent>
    </Dialog>
</template>
