export interface StringUtils {
  removeEndDot: (text: string) => string;
  removeEndDotAndReplace: (text: string, replaceWith?: string) => string;
  endsWithDot: (text: string) => boolean;
}
