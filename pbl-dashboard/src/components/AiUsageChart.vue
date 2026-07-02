<template>
    <div class="card shadow border-0">

        <div class="card-body">

            <h5 class="mb-4">
                Grafik Penggunaan AI
            </h5>

            <Bar
                :data="chartData"
                :options="chartOptions"
            />

        </div>

    </div>
</template>

<script setup>

import {

Bar

} from "vue-chartjs"

import {

Chart as ChartJS,

CategoryScale,

LinearScale,

BarElement,

Title,

Tooltip,

Legend

} from "chart.js"

import {

computed

} from "vue"

ChartJS.register(

CategoryScale,

LinearScale,

BarElement,

Title,

Tooltip,

Legend

)

const props = defineProps({

usages:Array

})

const chartData = computed(()=>({

labels:props.usages.map(

x=>x.nomor

),

datasets:[{

label:"Jumlah Penggunaan AI",

data:props.usages.map(

x=>x.jumlah

)

}]

}))

const chartOptions={

responsive:true,

plugins:{

legend:{

display:false

}

}

}

</script>