/**
 * Is comparing objects equal
 * @param {Record<string, unknown>} template - first object
 * @param {Record<string, unknown>} source - second object
 * @return {boolean}
 */
export const isEqualObjects = (template: Record<string, unknown>, source: Record<string, unknown>) => {
    if (template === null && source !== null) return false
    if (template !== null && source === null) return false
    if (template === null && source === null) return true
    return Object.keys(template).reduce(
        (acc: boolean, curr: string): boolean => template[curr] === source[curr] && acc,
        true,
    )
}

/**
 * Is object empty
 * @param {Record<string, unknown>} obj
 * @return {boolean}
 */
export const isEmpty = (obj: Record<string, unknown>) => (!obj ? true : !Object.keys(obj).length)

export const wait = async (delay: number, callback: () => void): Promise<number> =>
    await new Promise(resolve => window.setTimeout(callback, delay))