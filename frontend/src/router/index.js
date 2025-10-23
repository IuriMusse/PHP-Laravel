import { createRouter, createWebHistory } from "vue-router";
import ListaUsuario from "../views/ListaUsuario.vue";
import FormUsuario from "../views/FormUsuario.vue";
import EditarUsuario from "../views/EditarUsuario.vue";
import DetalheUsuario from "../views/DetalheUsuario.vue";

// Definição das rotas principais da aplicação
const routes = [
  // Rota de listagem (página inicial)
  { path: "/", component: ListaUsuario },

  // Rota para criação de novo usuário
  { path: "/create", component: FormUsuario },

  // Rota de edição, requer o ID do usuário como parâmetro
  { path: "/edit/:id", component: EditarUsuario },

  // Rota de detalhes, nomeada para facilitar o uso no router-link
  { path: "/detail/:id", name: "DetalheUsuario", component: DetalheUsuario },
];

// Criação da instância do Router
const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
