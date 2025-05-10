import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import Register from '../views/Register.vue'
import Login from '../views/Login.vue'
import Dashboard from '../views/Dashboard.vue'
import EmpresasList from '../views/empresa/EmpresasList.vue'
import EmpresaForm from '../views/empresa/EmpresaForm.vue'
import PessoasList from '../views/PessoasList.vue'
import PessoaForm from '../views/PessoaForm.vue'
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
    {
      path: '/empresas',
      name: 'Empresas',
      component: EmpresasList,
      meta: { requiresAuth: true },
    },
    {
      path: '/empresas/novo',
      name: 'EmpresaNew',
      component: EmpresaForm,
      meta: { requiresAuth: true },
    },
    {
      path: '/empresas/editar/:id',
      name: 'EmpresaEdit',
      component: EmpresaForm,
      meta: { requiresAuth: true },
    },
    {
      path: '/pessoas',
      name: 'Pessoas',
      component: PessoasList,
      meta: { requiresAuth: true },
    },
    {
      path: '/pessoas/novo',
      name: 'PessoaNew',
      component: PessoaForm,
      meta: { requiresAuth: true },
    },
    {
      path: '/pessoas/editar/:id',
      name: 'PessoaEdit',
      component: PessoaForm,
      meta: { requiresAuth: true },
    },
    // Catch-all 404 route
    {
      path: '/:pathMatch(.*)*',
      name: 'NotFound',
      redirect: () => {
        const auth = useAuth()
        if (auth.isAuthenticated()) {
          return { name: 'Dashboard' } // Redirect to Dashboard if logged in
        }
        return { name: 'Login' } // Redirect to Login if not logged in
      },
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
