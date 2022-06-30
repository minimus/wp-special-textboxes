import { IResponse, IWindowWithOptions } from '../../types/state'

export const getData = async (entry: string): Promise<IResponse> => {
	const {
		restData: { root, nonce },
	} = (window as IWindowWithOptions).stbUserOptions

	const req = new Request(`${root}stb/v6/admin/${entry}`, {
		method: 'GET',
		headers: {
			'X-WP-Nonce': nonce,
		},
		credentials: 'same-origin',
	})

	try {
		return await window.fetch(req).then((res) => res.json() as IResponse)
	} catch (e: unknown) {
		return e
	}
}

export const postData = async (entry: string, body?: string): Promise<IResponse> => {
	const {
		restData: { root, nonce },
	} = (window as IWindowWithOptions).stbUserOptions

	const req = new Request(`${root}stb/v6/admin/${entry}`, {
		method: 'POST',
		headers: {
			'X-WP-Nonce': nonce,
			'Content-Type': 'application/json',
		},
		credentials: 'same-origin',
		...(body ? { body } : {}),
	})

	try {
		return await window.fetch(req).then((res) => res.json() as IResponse)
	} catch (e: unknown) {
		return e
	}
}

export const deleteData = async (entry: string): Promise<IResponse> => {
	const {
		restData: { root, nonce },
	} = (window as IWindowWithOptions).stbUserOptions

	const req = new Request(`${root}stb/v6/admin/${entry}`, {
		method: 'DELETE',
		headers: {
			'X-WP-Nonce': nonce,
		},
		credentials: 'same-origin',
	})

	try {
		return await window.fetch(req).then((res) => res.json() as IResponse)
	} catch (e: unknown) {
		return e
	}
}
