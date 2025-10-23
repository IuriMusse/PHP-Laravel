// Arquivo: funcoes.js (Corrigido para 11 dígitos)
/**
 * Lógica comum de formatação/máscara de CPF para ser usada em qualquer lugar.
 * @param {string} rawValue - O valor do CPF (com ou sem formatação).
 * @returns {string} O CPF formatado (000.000.000-00).
 */
export const cpfFormatter = (rawValue) => {
  if (!rawValue) return "";
  let value = String(rawValue).replace(/\D/g, ""); // Limpa todos os não-dígitos

  // Se o valor tem exatamente 11 dígitos, aplica a formatação completa via regex (000.000.000-00)
  if (value.length === 11) {
    return value.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4");
  }

  // Lógica de formatação progressiva (para uso em @input)
  if (value.length > 3)
    value = value.substring(0, 3) + "." + value.substring(3);
  if (value.length > 7)
    value = value.substring(0, 7) + "." + value.substring(7);
  if (value.length > 11)
    value = value.substring(0, 11) + "-" + value.substring(11, 13);

  return value;
};

export const zipFormatter = (rawValue) => {
  let value = String(rawValue).replace(/\D/g, "");

  if (value.length > 8) value = value.substring(0, 8);

  if (value.length > 5) {
    value = value.substring(0, 5) + "-" + value.substring(5);
  }
  return value;
};
