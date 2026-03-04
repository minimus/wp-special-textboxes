import { IResponse, IWindowWithOptions } from '../../types/state';

export const getData = async (entry: string): Promise<IResponse | null> => {
  // eslint-disable-next-line @typescript-eslint/ban-ts-comment
  // @ts-expect-error
  const { restData: { root = '', nonce = '' } = {} } = (window as IWindowWithOptions).stbUserOptions ?? {};

  const req = new Request(`${root}stb/v6/admin/${entry}`, {
    method: 'GET',
    headers: {
      // eslint-disable-next-line @typescript-eslint/no-unsafe-assignment
      'X-WP-Nonce': nonce,
    },
    credentials: 'same-origin',
  });

  try {
    return await window.fetch(req).then((res) => res.json() as IResponse);
  } catch (e: unknown) {
    return null;
  }
};

export const postData = async (entry: string, body?: string): Promise<IResponse | null> => {
  const {
    // eslint-disable-next-line @typescript-eslint/ban-ts-comment
    // @ts-expect-error
    restData: { root = '', nonce = '' },
  } = (window as IWindowWithOptions).stbUserOptions ?? {};

  const req = new Request(`${root}stb/v6/admin/${entry}`, {
    method: 'POST',
    headers: {
      // eslint-disable-next-line @typescript-eslint/no-unsafe-assignment
      'X-WP-Nonce': nonce,
      'Content-Type': 'application/json',
    },
    credentials: 'same-origin',
    ...(body ? { body } : {}),
  });

  try {
    return await window.fetch(req).then((res) => res.json() as IResponse);
  } catch (e: unknown) {
    return null;
  }
};

export const deleteData = async (entry: string): Promise<IResponse | null> => {
  const {
    // eslint-disable-next-line @typescript-eslint/ban-ts-comment
    // @ts-expect-error
    restData: { root = '', nonce = '' },
  } = (window as IWindowWithOptions).stbUserOptions ?? {};

  const req = new Request(`${root}stb/v6/admin/${entry}`, {
    method: 'DELETE',
    headers: {
      // eslint-disable-next-line @typescript-eslint/no-unsafe-assignment
      'X-WP-Nonce': nonce,
    },
    credentials: 'same-origin',
  });

  try {
    return await window.fetch(req).then((res) => res.json() as IResponse);
  } catch (e: unknown) {
    return null;
  }
};
