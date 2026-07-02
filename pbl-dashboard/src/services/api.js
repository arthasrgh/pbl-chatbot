import axios from "axios"

const api = axios.create({

    baseURL: "http://127.0.0.1:8000/api",

    headers:{
        "Content-Type":"application/json"
    }

})

export const getStats = () => {
    return api.get("/stats")
}

export const getChart = () => {
    return api.get("/stats/chart")
}

export const getWordCloud = () => {
    return api.get("/stats/wordcloud")
}

export const getHotLeads = () => {
    return api.get("/stats/hotleads")
}

export const getAiStats = () => {
    return api.get("/stats/ai")
}

export const getAiUsages = () => {
    return api.get("/ai/usages")
}

export default api