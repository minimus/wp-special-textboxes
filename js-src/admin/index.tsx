import React from 'react';
import { createRoot } from 'react-dom/client';
import { SnackbarProvider } from 'notistack';
import { Provider } from 'react-redux';
import { HashRouter as Router } from 'react-router-dom';

import Application from './Application';
import store from './redux';

const container = document.getElementById('stb-admin-container');
// eslint-disable-next-line @typescript-eslint/ban-ts-comment
// @ts-expect-error
const root = createRoot(container);

root.render(
  <SnackbarProvider maxSnack={3}>
    <Provider store={store}>
      <Router>
        <Application />
      </Router>
    </Provider>
  </SnackbarProvider>,
);
