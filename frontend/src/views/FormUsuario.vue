<template>
  <div class="container">
    <h1>Cadastrar Usuário</h1>

    <form @submit.prevent="createUser">
      <div class="main-form-grid">
        <div class="form-group span-2">
          <label class="label-required">Nome:</label>
          <input v-model="user.name" required />
        </div>

        <div class="form-group span-2">
          <label class="label-required">Email:</label>
          <div class="input-tooltip-container">
            <input
              v-model="user.email"
              type="email"
              required
              :class="{ 'input-error': validationTooltip.email }"
            />
            <div
              v-if="validationTooltip.email"
              class="validation-tooltip-box"
              :data-message="validationTooltip.email"
            >
              <span class="warning-icon">!</span> {{ validationTooltip.email }}
            </div>
          </div>
        </div>

        <div class="form-group span-1">
          <label class="label-required">CPF:</label>
          <div class="input-tooltip-container">
            <input
              v-model="user.cpf"
              @input="maskCpf"
              maxlength="14"
              required
              :class="{ 'input-error': validationTooltip.cpf }"
            />
            <div
              v-if="validationTooltip.cpf"
              class="validation-tooltip-box"
              :data-message="validationTooltip.cpf"
            >
              <span class="warning-icon">!</span> {{ validationTooltip.cpf }}
            </div>
          </div>
        </div>

        <div class="form-group span-1 role-select-group">
          <label class="label-required">Perfil:</label>
          <div class="select-with-button">
            <select v-model="user.role_id" required>
              <option value="">Selecione...</option>
              <option v-for="role in roles" :key="role.id" :value="role.id">
                {{ role.name }}
              </option>
            </select>
            <button
              type="button"
              @click="showProfileInput = true"
              class="btn-quick-add"
              :disabled="showProfileInput"
              data-tooltip="Novo Perfil"
            >
              +
            </button>
          </div>
        </div>

        <div v-if="showProfileInput" class="form-group span-4 add-role-row">
          <input
            type="text"
            v-model="newProfileName"
            placeholder="Nome do Novo Perfil (Ex: Adminstrador)"
            @keyup.enter="createProfile"
            required
          />
          <button
            type="button"
            @click="createProfile"
            :disabled="!newProfileName"
            class="btn-add-role"
          >
            Salvar Perfil
          </button>
          <button
            type="button"
            @click="
              showProfileInput = false;
              newProfileName = '';
            "
            class="btn-cancel-role"
          >
            Cancelar
          </button>
        </div>

        <h3 class="grid-title span-4">Novo Endereço</h3>

        <div class="form-group span-2">
          <label class="label-required">Rua/Logradouro:</label>
          <input
            type="text"
            v-model="newAddress.street"
            placeholder="Rua, Avenida, etc."
          />
        </div>
        <div class="form-group span-1">
          <label class="label-required">Número:</label>
          <input type="text" v-model="newAddress.number" placeholder="123" />
        </div>

        <div class="form-group span-1">
          <label class="label-required">CEP:</label>
          <input
            type="text"
            v-model="newAddress.zip"
            placeholder="00000-000"
            maxlength="9"
            @input="maskZip"
          />
        </div>

        <div class="form-group span-2">
          <label class="label-required">Cidade:</label>
          <input type="text" v-model="newAddress.city" placeholder="Salvador" />
        </div>
        <div class="form-group span-1">
          <label class="label-required">Estado:</label>
          <input type="text" v-model="newAddress.state" placeholder="BA" />
        </div>

        <div class="form-group span-1 add-button-container">
          <label>&nbsp;</label>
          <button
            type="button"
            @click="addOrUpdateAddress"
            :disabled="!isNewAddressValid"
            class="btn-add-address"
          >
            ➕ Adicionar
          </button>
        </div>
      </div>

      <AddressForm
        v-model:addresses="user.addresses"
        @start-edit="loadAddressForEdit"
        :editing-index="editingAddressIndex"
        :can-edit="false"
      />

      <div class="form-actions">
        <router-link to="/" class="btn-back"> ← Voltar </router-link>

        <button
          type="submit"
          class="btn-save"
          :disabled="user.addresses.length === 0"
        >
          Salvar
        </button>
      </div>
    </form>

    <p v-if="message" class="success">{{ message }}</p>
    <p v-if="error" class="error">{{ error }}</p>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { useRouter } from "vue-router";
import api from "../services/api";
import AddressForm from "../components/FormEndereco.vue";
import { cpfFormatter, zipFormatter } from "../utils/funcoes";

const router = useRouter();

const user = ref({
  name: "",
  email: "",
  cpf: "",
  role_id: "",
  addresses: [],
});

const roles = ref([]);
const message = ref("");
const error = ref("");

const showProfileInput = ref(false);
const newProfileName = ref("");

const newAddress = ref({
  street: "",
  number: "",
  city: "",
  state: "",
  zip: "",
});
const editingAddressIndex = ref(null);

const isEditingAddress = computed(() => editingAddressIndex.value !== null);

// NOVO: Estado para armazenar a mensagem de erro no formato tooltip
const validationTooltip = ref({
  email: "",
  cpf: "",
});

function getEmptyAddress() {
  return { street: "", number: "", city: "", state: "", zip: "" };
}

// Máscara de CEP
function maskZip(event) {
  newAddress.value.zip = zipFormatter(event.target.value);
}

// Validação ajustada para exigir 8 dígitos de CEP e habilitar o botão ADICIONAR
const isNewAddressValid = computed(() => {
  const zipDigits = newAddress.value.zip.replace(/\D/g, ""); // Pega apenas os dígitos

  return (
    newAddress.value.street &&
    newAddress.value.number &&
    newAddress.value.city &&
    newAddress.value.state &&
    zipDigits.length === 8
  );
});

function addOrUpdateAddress() {
  if (!isNewAddressValid.value) return;

  user.value.addresses.push({ ...newAddress.value });

  newAddress.value = getEmptyAddress();
}

function loadAddressForEdit(data) {
  // Não faz nada em tela de cadastro (intencional)
}

function maskCpf(event) {
  user.value.cpf = cpfFormatter(event.target.value);
}

async function loadProfiles() {
  try {
    const response = await api.get("/roles");
    roles.value = response.data;
  } catch (err) {
    error.value = "Erro ao carregar perfis.";
  }
}

async function createProfile() {
  if (!newProfileName.value) return;

  try {
    const response = await api.post("/roles", {
      name: newProfileName.value,
    });
    const newProfile = response.data;

    roles.value.push(newProfile);
    user.value.role_id = newProfile.id;

    newProfileName.value = "";
    showProfileInput.value = false;

    message.value = `Perfil '${newProfile.name}' adicionado com sucesso!`;
    setTimeout(() => (message.value = ""), 3000);
  } catch (err) {
    error.value = "Erro ao criar novo perfil.";
    setTimeout(() => (error.value = ""), 3000);
    console.error(err);
  }
}

function clearValidationTooltips() {
  validationTooltip.value.email = "";
  validationTooltip.value.cpf = "";
}

async function createUser() {
  clearValidationTooltips(); // Limpa tooltips de erro antes de enviar

  try {
    const dataToSend = { ...user.value };
    if (dataToSend.cpf) {
      dataToSend.cpf = dataToSend.cpf.replace(/\D/g, "");
    }

    // Limpa a máscara de CEP de todos os endereços antes de enviar ao backend
    const finalAddresses = dataToSend.addresses.map((addr) => ({
      ...addr,
      zip: addr.zip.replace(/\D/g, ""),
    }));
    dataToSend.addresses = finalAddresses;

    await api.post("/users", dataToSend);

    message.value = "Usuário cadastrado com sucesso!";
    error.value = "";

    setTimeout(() => router.push("/"), 2500);
  } catch (err) {
    message.value = "";
    error.value = "Erro ao cadastrar usuário.";

    if (err.response && err.response.data.errors) {
      const errors = err.response.data.errors;

      // Processa e armazena erros para o tooltip (em português)
      if (errors.email) {
        validationTooltip.value.email = "O email informado já está em uso.";
      }
      if (errors.cpf) {
        validationTooltip.value.cpf = "O CPF informado já está em uso.";
      }
    }
  }
}

onMounted(loadProfiles);
</script>

<style scoped></style>
