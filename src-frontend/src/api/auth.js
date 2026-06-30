import axios from 'axios';
import api from '@/api/index'

const {
    http
  } = api

export default {
  baseUrl: `${import.meta.env.VITE_API_BASE_URL}/api`,

  login(payload) {
    return axios.post(`${this.baseUrl}/login`, payload)
  },

  signup(payload) {
    return axios.post(`${this.baseUrl}/signup`, payload)
  },

  logout() {
    return http(`${this.baseUrl}/logout`).post()
  },

  getUserDetails() {
    return http(`${this.baseUrl}/user`).get()
  }
}