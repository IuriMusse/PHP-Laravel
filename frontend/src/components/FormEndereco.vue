<template>
  <div class="address-list-container" v-if="localAddresses.length">
    <div class="address-list-header">
      <h4>Endereços Cadastrados ({{ localAddresses.length }})</h4>
      <slot name="add-button"></slot>
    </div>

    <table class="user-table">
      <thead>
        <tr>
          <th>Rua/Logradouro</th>
          <th>Número</th>
          <th>CEP</th>
          <th>Cidade</th>
          <th>Estado</th>
          <th>Ação</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(address, index) in localAddresses" :key="index">
          <td>{{ address.street }}</td>
          <td>{{ address.number }}</td>
          <td>{{ address.zip }}</td>
          <td>{{ address.city }}</td>
          <td>{{ address.state }}</td>
          <td>
            <button
              type="button"
              @click="editAddress(index)"
              class="btn-edit-grid"
              :disabled="!props.canEdit"
              data-tooltip="Editar"
            >
              ✏️
            </button>
            <button
              type="button"
              @click="removeAddress(index)"
              class="btn-remove-grid"
              :disabled="!props.canEdit"
              data-tooltip="Excluir"
            >
              🗑️
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <p v-else class="empty-list">Nenhum endereço vinculado.</p>
</template>

<script setup>
import { ref, watch } from "vue";

const props = defineProps({
  addresses: { type: Array, default: () => [] },
  // Propriedade para informar o índice do endereço que está sendo editado
  editingIndex: { type: [Number, null], default: null },
  // Propriedade para controlar se a edição e remoção estão habilitadas
  canEdit: { type: Boolean, default: true },
});

const emit = defineEmits(["update:addresses", "start-edit"]);
const localAddresses = ref([]);

function removeAddress(index) {
  if (confirm("Tem certeza que deseja remover este endereço da lista?")) {
    localAddresses.value.splice(index, 1);
  }
}

// Lógica para emitir o endereço a ser editado para o componente pai
function editAddress(index) {
  // Move os dados do endereço para o formulário de nova entrada no componente pai
  emit("start-edit", { address: localAddresses.value[index], index: index });
}

// Sincroniza a lista de endereços do componente pai
watch(
  () => props.addresses,
  (newAddresses) => {
    if (newAddresses && newAddresses.length >= 0) {
      localAddresses.value = newAddresses.map((addr) => ({
        street: addr.street || "",
        number: addr.number || "",
        city: addr.city || "",
        state: addr.state || "",
        zip: addr.zip || "",
      }));
    }
  },
  { immediate: true, deep: true }
);

// Emite o array para o componente pai a cada alteração
watch(
  localAddresses,
  (newVal) => {
    emit("update:addresses", newVal);
  },
  { deep: true }
);
</script>

<style scoped></style>
