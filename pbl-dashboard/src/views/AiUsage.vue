<template>
<div class="container-fluid py-4">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2 class="fw-bold">
            🤖 Monitoring Penggunaan AI
        </h2>

        <button
            class="btn btn-danger"
            @click="loadData"
        >
            🔄 Refresh
        </button>

    </div>

    <!-- CARD -->
    <div class="row g-4 mb-4">

        <div class="col-lg-3 col-md-6">

            <div class="card shadow border-0 h-100">

                <div class="card-body">

                    <h6 class="text-secondary">
                        Total Pengguna AI
                    </h6>

                    <h2 class="fw-bold text-primary">
                        {{ totalUser }}
                    </h2>

                </div>

            </div>

        </div>

        <div class="col-lg-3 col-md-6">

            <div class="card shadow border-0 h-100">

                <div class="card-body">

                    <h6 class="text-secondary">
                        Total Request
                    </h6>

                    <h2 class="fw-bold text-success">
                        {{ totalRequest }}
                    </h2>

                </div>

            </div>

        </div>

        <div class="col-lg-3 col-md-6">

            <div class="card shadow border-0 h-100">

                <div class="card-body">

                    <h6 class="text-secondary">
                        Hampir Habis
                    </h6>

                    <h2 class="fw-bold text-warning">
                        {{ warning }}
                    </h2>

                </div>

            </div>

        </div>

        <div class="col-lg-3 col-md-6">

            <div class="card shadow border-0 h-100">

                <div class="card-body">

                    <h6 class="text-secondary">
                        Kuota Habis
                    </h6>

                    <h2 class="fw-bold text-danger">
                        {{ habis }}
                    </h2>

                </div>

            </div>

        </div>

    </div>

    <!-- CHART -->

    <div class="card shadow border-0 mb-4">

        <div class="card-body">

            <Bar
                :data="chartData"
                :options="chartOptions"
            />

        </div>

    </div>

    <!-- SEARCH -->

    <div class="card shadow border-0 mb-3">

        <div class="card-body">

            <input

                v-model="keyword"

                class="form-control"

                placeholder="Cari nomor WhatsApp..."

            >

        </div>

    </div>

    <!-- TABLE -->

    <div class="card shadow border-0">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-dark">

                    <tr>

                        <th>No</th>

                        <th>Nomor WhatsApp</th>

                        <th>Total Penggunaan</th>

                        <th>Progress</th>

                        <th>Sisa Kuota</th>

                        <th>Status</th>

                    </tr>

                </thead>

                <tbody>

                    <tr
                        v-for="(item,index) in filteredUsages"
                        :key="item.id"
                    >

                        <td>{{ index+1 }}</td>

                        <td>{{ item.nomor }}</td>

                        <td>{{ item.jumlah }}</td>

                        <td width="250">

                            <div class="progress">

                                <div

                                    class="progress-bar"

                                    :class="progressColor(item.jumlah)"

                                    :style="{

                                        width:(item.jumlah/20*100)+'%'

                                    }"

                                >

                                    {{ item.jumlah }}/20

                                </div>

                            </div>

                        </td>

                        <td>

                            {{ Math.max(20-item.jumlah,0) }}

                        </td>

                        <td>

                            <span

                                class="badge"

                                :class="badgeColor(item.jumlah)"

                            >

                                {{ status(item.jumlah) }}

                            </span>

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</div>
</template>

<script setup>

import {

ref,

computed,

onMounted,

onUnmounted

} from "vue"

import { getAiUsages } from "../services/api"

import {

Chart as ChartJS,

CategoryScale,

LinearScale,

BarElement,

Title,

Tooltip,

Legend

} from "chart.js"

import { Bar } from "vue-chartjs"

ChartJS.register(

CategoryScale,

LinearScale,

BarElement,

Title,

Tooltip,

Legend

)

const usages = ref([])

const keyword = ref("")

const loadData = async()=>{

    try{

        const res = await getAiUsages()

        usages.value = res.data

    }

    catch(err){

        console.log(err)

    }

}

const filteredUsages = computed(()=>{

    return usages.value.filter(item=>

        item.nomor.includes(keyword.value)

    )

})

const totalUser = computed(()=>{

    return usages.value.length

})

const totalRequest = computed(()=>{

    return usages.value.reduce(

        (a,b)=>a+b.jumlah,

        0

    )

})

const warning = computed(()=>{

    return usages.value.filter(

        x=>x.jumlah>=15 && x.jumlah<20

    ).length

})

const habis = computed(()=>{

    return usages.value.filter(

        x=>x.jumlah>=20

    ).length

})

const progressColor=(jumlah)=>{

    if(jumlah>=20) return "bg-danger"

    if(jumlah>=15) return "bg-warning"

    return "bg-success"

}

const badgeColor=(jumlah)=>{

    if(jumlah>=20) return "bg-danger"

    if(jumlah>=15) return "bg-warning text-dark"

    return "bg-success"

}

const status=(jumlah)=>{

    if(jumlah>=20) return "Habis"

    if(jumlah>=15) return "Hampir Habis"

    return "Aman"

}

const chartData = computed(()=>({

labels:usages.value.map(

x=>x.nomor

),

datasets:[{

label:"Penggunaan AI",

data:usages.value.map(

x=>x.jumlah

),

backgroundColor:"#dc3545"

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

let interval=null

onMounted(()=>{

    loadData()

    interval=setInterval(

        loadData,

        10000

    )

})

onUnmounted(()=>{

    clearInterval(interval)

})

</script>

<style scoped>

.card{

border-radius:18px;

}

.progress{

height:22px;

}

.progress-bar{

font-size:12px;

font-weight:bold;

}

.table td{

vertical-align:middle;

}

</style>