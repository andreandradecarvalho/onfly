import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import Register from '../views/Register.vue'
import Login from '../views/Login.vue'
import Dashboard from '../views/Dashboard.vue'
import { useAuth } from '../stores/auth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  scrollBehavior(to, from, savedPosition) {
    return savedPosition || { left: 0, top: 0 }
  },
  routes: [
    {
      path: '/',
      name: 'Home',
      component: HomeView,
    },
    {
      path: '/register',
      name: 'Cadastrar',
      component: Register,
    },
    {
      path: '/login',
      name: 'Login',
      component: Login,
    },
    {
      path: '/dashboard',
      name: 'Dashboard',
      component: Dashboard,
      meta: { requiresAuth: true },
    },
  ],
})

router.beforeEach(async (to, from, next) => {
  const auth = useAuth()
  // Se a rota requer autenticação
  if (to.meta.requiresAuth) {
    // Se ainda não estiver autenticado, redirecionar para login
    if (!auth.isAuthenticated()) {
      return next('/login')
    }
  }
  next()
})

export default router
