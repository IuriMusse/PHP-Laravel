<template>
  <div class="container" v-if="user.id">
    <h1>Detalhes do Usuário: {{ user.name }}</h1>

    <div class="details-card">
      <h2>Informações Pessoais</h2>
      <p><strong>Nome:</strong> {{ user.name }}</p>
      <p><strong>Email:</strong> {{ user.email }}</p>
      <p><strong>CPF:</strong> {{ cpfFormatter(user.cpf) }}</p>
      <p><strong>Perfil:</strong> {{ user.role?.name || "Não atribuído" }}</p>
      <p>
        <strong>Data de Cadastro:</strong> {{ formatDate(user.created_at) }}
      </p>
    </div>

    <div
      class="details-card"
      v-if="user.addresses && user.addresses.length > 0"
    >
      <h2>Endereços ({{ user.addresses.length }})</h2>
      <div
        v-for="(address, index) in user.addresses"
        :key="index"
        class="address-item"
      >
        <h4>Endereço #{{ index + 1 }}</h4>
        <p><strong>Rua:</strong> {{ address.street }}, {{ address.number }}</p>
        <p>
          <strong>Cidade/Estado:</strong> {{ address.city }} -
          {{ address.state }}
        </p>
        <p><strong>CEP:</strong> {{ address.zip }}</p>
      </div>
    </div>

    <div class="form-actions">
      <router-link to="/" class="btn-back"> ← Voltar </router-link>
      <router-link :to="`/edit/${user.id}`" class="btn-edit-detail">
        ✏️ Editar Cadastro
      </router-link>
    </div>
  </div>
  <div class="container" v-else>
    <p v-if="loading">Carregando detalhes do usuário...</p>
    <p v-else-if="error" class="error">{{ error }}</p>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import api from "../services/api";
import { cpfFormatter, zipFormatter } from "../utils/funcoes";

const route = useRoute();
const user = ref({});
const loading = ref(true);
const error = ref(null);

function formatDate(dateString) {
  if (!dateString) return "N/A";
  const options = {
    year: "numeric",
    month: "long",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  };
  // Usa Date.parse para lidar com diferentes formatos de timestamp
  return new Date(Date.parse(dateString)).toLocaleDateString("pt-BR", options);
}

// Busca os detalhes do usuário específico usando o ID da rota
async function fetchUser() {
  try {
    loading.value = true;
    const response = await api.get(`/users/${route.params.id}`);

    const userData = response.data.data;

    if (!userData.addresses) {
      userData.addresses = [];
    }

    // Formatação de CEP para exibição na tela de detalhes
    userData.addresses = userData.addresses.map((addr) => {
      addr.zip = zipFormatter(addr.zip);
      return addr;
    });

    user.value = userData;
  } catch (err) {
    error.value = "Erro ao carregar os detalhes do usuário.";
    console.error(err);
  } finally {
    loading.value = false;
  }
}

onMounted(fetchUser);
</script>

<style scoped></style>
