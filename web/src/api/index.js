import axios from 'axios'

export default {
    http (baseUrl) {
        const axiosClient = axios.create({
            baseURL: baseUrl
        })

        axiosClient.interceptors.request.use((config) => {
            const token = localStorage.getItem('ACCESS_TOKEN');
            config.headers.Authorization = `Bearer ${token}`
            return config;
        })
          
        axiosClient.interceptors.response.use((response) => {
        return response
        }, (error) => {
        const {response} = error;
        if (response.status === 401) {
            localStorage.removeItem('ACCESS_TOKEN')
        } else if (response.status === 404) {
            //Show not found
        }
        
        throw error;
        })

        return axiosClient
  },

  index (params = null) {
    return this.http(this.baseUrl)
      .get(this.url, { params })
  },

  show (id, params = null) {
    return this.http(this.baseUrl)
      .get(`${this.url}/${id}`, { params })
  },

  store (payload) {
    return this.http(this.baseUrl)
      .post(`${this.url}`, payload)
  },

  update (id, payload) {
    return this.http(this.baseUrl)
      .put(`${this.url}/${id}`, payload)
  },

  search (payload, headers = {}) {
    return this.http(this.baseUrl)
      .post(`${this.url}/search`, payload, { headers })
  },

  destroy (id) {
    return this.http(this.baseUrl)
      .delete(`${this.url}/${id}`)
  }
}