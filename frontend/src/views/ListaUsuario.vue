<template>
  <div class="container">
    <h1>Lista de Usuários</h1>

    <div class="search-form">
      <h3>Pesquisa (Filtros: Nome, CPF e Período de Cadastro)</h3>
      <div class="form-row">
        <div class="form-group">
          <label>Nome:</label>
          <input type="text" v-model="filters.name" @keyup.enter="fetchUsers" />
        </div>
        <div class="form-group">
          <label>CPF:</label>
          <input
            type="text"
            v-model="filters.cpf"
            @input="maskCpf"
            maxlength="14"
            @keyup.enter="fetchUsers"
          />
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Período Inicial (Cadastro):</label>
          <input type="date" v-model="filters.date_start" />
        </div>
        <div class="form-group">
          <label>Período Final (Cadastro):</label>
          <input type="date" v-model="filters.date_end" />
        </div>
      </div>
      <div class="search-actions">
        <button @click="fetchUsers" class="btn-search">🔍 Buscar</button>
        <button @click="clearFilters" class="btn-clear">Limpar Filtros</button>
      </div>
    </div>

    <div class="list-actions-row">
      <div class="list-buttons">
        <router-link to="/create" class="btn-new">➕ Novo Usuário</router-link>
        <button @click="fetchUsers" class="btn-refresh">
          🔄 Atualizar Lista
        </button>
      </div>

      <div class="per-page-selector">
        <label>Itens por Página:</label>
        <select v-model.number="perPage" @change="fetchUsers(1)">
          <option :value="5">5</option>
          <option :value="10">10</option>
          <option :value="25">25</option>
        </select>
      </div>
    </div>

    <table v-if="users.length" class="user-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nome</th>
          <th>Data Cadastro</th>
          <th>Email</th>
          <th>CPF</th>
          <th>Perfil</th>
          <th>Endereços</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in users" :key="user.id">
          <td>{{ user.id }}</td>
          <td>{{ user.name }}</td>
          <td>
            {{
              user.created_at
                ? new Date(Date.parse(user.created_at)).toLocaleDateString(
                    "pt-BR"
                  )
                : "N/A"
            }}
          </td>
          <td>{{ user.email }}</td>
          <td>{{ user.cpf }}</td>
          <td>{{ user.role?.name || "—" }}</td>
          <td>{{ user.addresses?.length || 0 }} Endereço(s)</td>
          <td>
            <router-link
              :to="`/detail/${user.id}`"
              class="btn-detail"
              data-tooltip="Ver Detalhes"
              >👁️</router-link
            >
            <router-link
              :to="`/edit/${user.id}`"
              class="btn-edit"
              data-tooltip="Editar"
              >✏️</router-link
            >
            <button
              @click="deleteUser(user.id)"
              class="btn-delete"
              data-tooltip="Excluir"
            >
              🗑️
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <p v-else class="empty">Nenhum usuário encontrado 😕</p>

    <div v-if="pagination.last_page > 1" class="pagination-controls">
      <p>
        Página {{ pagination.current_page }} de
        {{ pagination.last_page }} (Total: {{ pagination.total }} usuários)
      </p>

      <div class="pagination-buttons">
        <button
          @click="changePage(pagination.current_page - 1)"
          :disabled="pagination.current_page === 1"
          class="btn-page"
        >
          &laquo; Anterior
        </button>
        <button
          @click="changePage(pagination.current_page + 1)"
          :disabled="pagination.current_page === pagination.last_page"
          class="btn-page"
        >
          Próximo &raquo;
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "../services/api";
import { cpfFormatter } from "../utils/funcoes";

const users = ref([]);
const perPage = ref(5); // Quantidade de itens por página
const pagination = ref({
  current_page: 1,
  last_page: 1,
  total: 0,
  per_page: 5, // Valor inicial
});

// Para controlar a exibição do loading
const loading = ref(false);

const filters = ref({
  name: "",
  cpf: "",
  date_start: "",
  date_end: "",
});

function maskCpf(event) {
  filters.value.cpf = cpfFormatter(event.target.value);
}

// Busca a lista de usuários, aceitando um número de página e o tamanho da página
async function fetchUsers(page = 1) {
  try {
    loading.value = true;
    const params = { page, per_page: perPage.value };

    if (filters.value.name) params.name = filters.value.name;

    if (filters.value.cpf) {
      params.cpf = filters.value.cpf.replace(/\D/g, "");
    }

    if (filters.value.date_start) params.date_start = filters.value.date_start;
    if (filters.value.date_end) params.date_end = filters.value.date_end;

    const response = await api.get("/users", { params: params });

    users.value = response.data.data;
    pagination.value = {
      current_page: response.data.meta.current_page,
      last_page: response.data.meta.last_page,
      total: response.data.meta.total,
      per_page: response.data.meta.per_page,
    };
  } catch (error) {
    console.error("Erro ao carregar usuários:", error);
    alert("Erro ao carregar usuários!");
  } finally {
    loading.value = false;
  }
}

function changePage(page) {
  if (page >= 1 && page <= pagination.value.last_page) {
    fetchUsers(page);
  }
}

function clearFilters() {
  filters.value = {
    name: "",
    cpf: "",
    date_start: "",
    date_end: "",
  };
  fetchUsers(1);
}

async function deleteUser(id) {
  if (confirm("Tem certeza que deseja excluir este usuário?")) {
    try {
      await api.delete(`/users/${id}`);
      fetchUsers(pagination.value.current_page);
      alert("Usuário excluído com sucesso!");
    } catch (error) {
      alert("Erro ao excluir usuário.");
    }
  }
}

onMounted(fetchUsers);
</script>

<style scoped></style>
