<template>
  <div class="container form-page">
    <h1>Editar Usuário</h1>

    <form @submit.prevent="updateUser">
      <div class="main-form-grid">
        <div class="form-group span-4">
          <label class="label-required">Nome:</label>
          <input v-model="user.name" required />
        </div>

        <div class="form-group span-4">
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

        <div class="form-group span-2">
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

        <div class="form-group span-2">
          <label class="label-required">Perfil:</label>
          <select v-model.number="user.role_id" required>
            <option value="">Selecione...</option>
            <option v-for="role in roles" :key="role.id" :value="role.id">
              {{ role.name }}
            </option>
          </select>
        </div>

        <template v-if="isEditingAddress || isAddingAddress">
          <h3 class="grid-title span-4">
            {{ isEditingAddress ? "Editando Endereço" : "Novo Endereço" }}
          </h3>

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
            <input
              type="text"
              v-model="newAddress.city"
              placeholder="Salvador"
            />
          </div>
          <div class="form-group span-1">
            <label class="label-required">Estado:</label>
            <input type="text" v-model="newAddress.state" placeholder="BA" />
          </div>

          <div class="form-group span-1 add-button-container">
            <label>&nbsp;</label>
            <div class="button-group-row">
              <button
                type="button"
                @click="cancelAddressOperation"
                class="btn-cancel-address"
              >
                ❌ Cancelar
              </button>
              <button
                type="button"
                @click="addOrUpdateAddress"
                :disabled="!isNewAddressValid"
                class="btn-add-address"
              >
                {{ isEditingAddress ? "✔️ Salvar Edição" : "➕ Adicionar" }}
              </button>
            </div>
          </div>
        </template>
      </div>

      <AddressForm
        v-model:addresses="user.addresses"
        @start-edit="loadAddressForEdit"
        :editing-index="editingAddressIndex"
        :can-edit="!isAddingAddress && !isEditingAddress"
      >
        <template #add-button>
          <button
            type="button"
            @click="startAddingAddress"
            class="btn-new-address"
            v-if="!isAddingAddress && !isEditingAddress"
          >
            ➕ Novo Endereço
          </button>
        </template>
      </AddressForm>

      <div class="form-actions">
        <router-link to="/" class="btn-back"> ← Voltar </router-link>

        <button
          type="submit"
          class="btn-save"
          :disabled="isEditingAddress || isAddingAddress"
        >
          Salvar Alterações
        </button>
      </div>
    </form>

    <p v-if="message" class="success">{{ message }}</p>
    <p v-if="error" class="error">{{ error }}</p>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import api from "../services/api";
import AddressForm from "../components/FormEndereco.vue";
import { cpfFormatter, zipFormatter } from "../utils/funcoes";

const route = useRoute();
const router = useRouter();

const user = ref({});
const roles = ref([]);
const message = ref("");
const error = ref("");

// Estado para armazenar a mensagem de erro no formato tooltip
const validationTooltip = ref({
  email: "",
  cpf: "",
});

const isAddingAddress = ref(false);

const newAddress = ref({
  street: "",
  number: "",
  city: "",
  state: "",
  zip: "",
});
const editingAddressIndex = ref(null);

const isEditingAddress = computed(() => editingAddressIndex.value !== null);

// ESTA É A ÚNICA DECLARAÇÃO DA FUNÇÃO QUE DEVE EXISTIR
function getEmptyAddress() {
  return { street: "", number: "", city: "", state: "", zip: "" };
}

// Máscara de CEP
function maskZip(event) {
  newAddress.value.zip = zipFormatter(event.target.value);
  return newAddress.value.zip;
}

// Validação ajustada para exigir 8 dígitos (sem o hífen)
const isNewAddressValid = computed(() => {
  const zipDigits = newAddress.value.zip.replace(/\D/g, "");

  return (
    newAddress.value.street &&
    newAddress.value.number &&
    newAddress.value.city &&
    newAddress.value.state &&
    zipDigits.length === 8
  );
});

function startAddingAddress() {
  isAddingAddress.value = true;
  newAddress.value = getEmptyAddress();
}

function cancelAddressOperation() {
  isAddingAddress.value = false;
  editingAddressIndex.value = null;
  newAddress.value = getEmptyAddress();
}

function addOrUpdateAddress() {
  if (!isNewAddressValid.value) return;

  const addressToSave = { ...newAddress.value };

  if (isEditingAddress.value) {
    user.value.addresses[editingAddressIndex.value] = addressToSave;
  } else if (isAddingAddress.value) {
    user.value.addresses.push(addressToSave);
  }

  cancelAddressOperation();
}

// Recebe o evento do AddressForm para carregar os dados para edição
function loadAddressForEdit({ address, index }) {
  if (isAddingAddress.value) return;

  const addressWithMask = {
    ...address,
    zip: address.zip ? zipFormatter(address.zip) : "",
  };

  newAddress.value = addressWithMask;
  editingAddressIndex.value = index;
}

// Funções de Máscara (copiada)
function maskCpf(event) {
  user.value.cpf = cpfFormatter(event.target.value);
  return user.value.cpf;
}

async function loadData() {
  try {
    const [userRes, profileRes] = await Promise.all([
      api.get(`/users/${route.params.id}`),
      api.get("/roles"),
    ]);

    const userData = userRes.data.data;

    if (!userData.addresses) {
      userData.addresses = [];
    }

    // Atribui o ID da Role à chave role_id para o v-model (como Number)
    userData.role_id = userData.role_id ? Number(userData.role_id) : "";

    // Aplica as formatações necessárias antes de atribuir ao estado
    if (userData.cpf) {
      maskCpf({ target: { value: userData.cpf.replace(/\D/g, "") } });
    }

    // Aplica a máscara de CEP nos endereços carregados para exibição na grid
    userData.addresses = userData.addresses.map((addr) => {
      return {
        ...addr,
        zip: addr.zip ? zipFormatter(addr.zip) : addr.zip,
      };
    });

    user.value = userData;
    roles.value = profileRes.data;
  } catch (err) {
    error.value = "Erro ao carregar dados.";
  }
}

function clearValidationTooltips() {
  validationTooltip.value.email = "";
  validationTooltip.value.cpf = "";
}

async function updateUser() {
  clearValidationTooltips(); // Limpa tooltips de erro antes de enviar

  if (isEditingAddress.value || isAddingAddress.value) {
    alert(
      "Por favor, finalize a edição/adição do endereço antes de salvar o usuário."
    );
    return;
  }

  try {
    const dataToSend = { ...user.value };
    if (dataToSend.cpf) {
      dataToSend.cpf = dataToSend.cpf.replace(/\D/g, "");
    }

    // Limpa a máscara de CEP dos endereços antes de enviar ao backend
    const finalAddresses = dataToSend.addresses.map((addr) => ({
      ...addr,
      zip: addr.zip.replace(/\D/g, ""),
    }));
    dataToSend.addresses = finalAddresses;

    await api.put(`/users/${route.params.id}`, dataToSend);
    message.value = "Usuário atualizado com sucesso!";

    setTimeout(() => router.push("/"), 1500);
  } catch (err) {
    error.value = "Erro ao atualizar usuário.";

    if (err.response && err.response.data.errors) {
      const errors = err.response.data.errors;

      // Processa e armazena erros para o tooltip
      if (errors.email) {
        validationTooltip.value.email = "O email informado já está em uso.";
      }
      if (errors.cpf) {
        validationTooltip.value.cpf = "O CPF informado já está em uso.";
      }
      // O alert global foi removido.
    }
  }
}

onMounted(loadData);
</script>
<style scoped></style>
