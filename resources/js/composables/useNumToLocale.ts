import { ref, toValue } from 'vue';

/**
 * Opciones para formatear números con toLocaleString.
 */
type LocaleOptions = {
  minimumFractionDigits: number;
  maximumFractionDigits: number;
};

/**
 * Composable para formatear números según una configuración regional.
 * @returns {Object} locale - Ref con el locale actual (por defecto 'es-VE').
 * @returns {Object} options - Ref con las opciones de formato.
 * @returns {Function} numToLocale - Función que formatea un número al locale especificado.
 */
export function useNumToLocale() {
  const locale = ref<string>('es-VE');
  const options = ref<LocaleOptions>({
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  });

  /**
   * Formatea un número según el locale y las opciones configuradas.
   * @param {number} num - Número a formatear.
   * @returns {string} Número formateado como string.
   */
  const numToLocale = (num: number): string =>
    num ? toValue(num).toLocaleString(locale.value, options.value) : '0,00';

  return { locale, options, numToLocale };
}
