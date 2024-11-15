import api from '@/api/index'

const {
  http,
  index,
  show,
  store,
  update,
  destroy
} = api

export default {
  baseUrl: `${import.meta.env.VITE_API_BASE_URL}/api`,
  url: '/users',
  http,
  index,
  show,
  store,
  update,
  destroy
}