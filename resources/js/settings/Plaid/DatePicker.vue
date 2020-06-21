<template>
    <div class="mb-5 w-64" :style="showDatepicker ?'height: 340px' : 'height: 3rem;'">

        <label class="font-bold mb-1 block" v-dark-mode-light-text>Select Date</label>
        <div class="relative">
            <div @click="() => showDatepicker = true" class="w-full">
                <input type="hidden" name="date" v-model="date">
                <input
                    type="text"
                    readonly
                    v-model="datepickerValue"
                    @keydown.escape="() => showDatepicker = false"
                    class="w-full pl-4 pr-10 py-3 leading-none rounded-lg shadow-sm focus:outline-none focus:shadow-outline font-medium"
                    v-dark-mode-input
                    placeholder="Select date"
                    style="width: 17rem;"
                >

                <div class="absolute top-0 right-0 px-3 py-2">
                    <svg class="h-6 w-6 text-gray-400"  fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>

            <div
                class="mt-12 rounded-lg shadow p-4 absolute top-0 left-0"
                v-dark-mode-input
                style="width: 17rem"
                v-show="showDatepicker">

                <div class="flex justify-between items-center mb-2">
                    <div>
                        <span v-text="months[month]" class="text-lg font-bold" v-dark-mode-dark-text></span>
                        <span v-text="year" class="ml-1 text-lg font-normal" v-dark-mode-light-text></span>
                    </div>
                    <div>
                        <button
                            type="button"
                            class="transition ease-in-out duration-100 inline-flex cursor-pointer p-1 rounded-full"
                            :class="{
                                 'hover:text-white hover:bg-blue-700': $store.getters.darkMode,
                                 'hover:bg-gray-200 hover:text-gray-500': !$store.getters.darkMode
                            }"
                            @click="goBackAMonth">
                            <svg class="h-6 w-6 inline-flex"  fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <button
                            type="button"
                            class="transition ease-in-out duration-100 inline-flex cursor-pointer p-1 rounded-full"
                            :class="{
                                 'hover:text-white hover:bg-blue-700': $store.getters.darkMode,
                                 'hover:bg-gray-200 hover:text-gray-500': !$store.getters.darkMode
                            }"
                            @click="goForwardAMonth">
                            <svg class="h-6 w-6 inline-flex"  fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex flex-wrap mb-3 -mx-1">
                    <div v-for="(day, index) in days" :key="index" class="flex-1">
                        <div class="px-1" @click="() => chooseDate(day)">
                            <div
                                v-text="day"
                                class="font-medium text-center text-xs" v-dark-mode-dark-text></div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap -mx-1">
                    <div v-for="blankday in blankdays" style="width: 14.28%"
                    >
                        <div
                            class="text-center border p-1 border-transparent text-sm"
                        ></div>
                    </div>
                    <div v-for="(date, dateIndex) in no_of_days" :key="dateIndex" style="width: 14.28%">
                        <div class="px-1 mb-1">
                            <div
                                @click="getDateValue(date)"
                                v-text="date"
                                class="cursor-pointer text-center text-sm leading-none rounded-full leading-loose transition ease-in-out duration-100"
                                :class="{
                                    'bg-blue-500 text-white': isToday(date) === true && !$store.getters.darkMode,
                                    'hover:text-gray-600 hover:bg-blue-200': isToday(date) === false && !$store.getters.darkMode,

                                    'bg-blue-800 text-white': isToday(date) === true && $store.getters.darkMode,
                                    'hover:text-gray-200 hover:bg-blue-700': isToday(date) === false && $store.getters.darkMode,

                                }"
                                v-dark-mode-dark-text
                            ></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div >

</template>
<script>
    export default {
        props: ['darkMode'],
        data() {
            return {
                showDatepicker: false,
                datepickerValue:  '',

                month: "",
                year: "",
                no_of_days: [],
                blankdays: [1,2,3,4],
                days: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
                months: [
                    "January",
                    "February",
                    "March",
                    "April",
                    "May",
                    "June",
                    "July",
                    "August",
                    "September",
                    "October",
                    "November",
                    "December"
                ],
                date: new Date,
                actualValue: '',
            };
        },
        methods: {
            goBackAMonth() {
                if (this.month === 0) {
                    this.month = 11;
                    this.year--;
                } else {
                    this.month--
                }

                this.getNoOfDays();
            },
            goForwardAMonth() {
                if (this.month === 11) {
                    this.month = 0;
                    this.year++;
                } else {
                    this.month++
                }

                this.getNoOfDays();
            },

            initDate() {
                let today = new Date();
                this.month = today.getMonth();
                this.year = today.getFullYear();
                this.datepickerValue = new Date(
                    this.year,
                    this.month,
                    today.getDate()
                ).toDateString();
            },

            isToday(date) {
                const today = new Date();
                const d = new Date(this.year, this.month, date);

                return today.toDateString() === d.toDateString() ? true : false;
            },

            getDateValue(date) {
                let selectedDate = new Date(this.year, this.month, date);
                this.datepickerValue = selectedDate.toDateString();

                Bus.$emit('choseDate',
                    selectedDate.getFullYear() +
                    "-" +
                    ("0" + selectedDate.getMonth()).slice(-2) +
                    "-" +
                    ("0" + selectedDate.getDate()).slice(-2));

                this.showDatepicker = false;
            },

            getNoOfDays() {
                let daysInMonth = new Date(this.year, this.month + 1, 0).getDate();

                // find where to start calendar day of week
                let dayOfWeek = new Date(this.year, this.month).getDay();
                let blankdaysArray = [];
                for (var i = 1; i <= dayOfWeek; i++) {
                    blankdaysArray.push(i);
                }

                let daysArray = [];
                for (var i = 1; i <= daysInMonth; i++) {
                    daysArray.push(i);
                }

                this.blankdays = blankdaysArray;
                this.no_of_days = daysArray;
            },
            chooseDate(day) {
                this.$emit('chosenDate', this.year + '-' + this.month + '-' + day);
            }
        },
        mounted() {
            this.initDate();
            this.getNoOfDays()
        }
    };
</script>
