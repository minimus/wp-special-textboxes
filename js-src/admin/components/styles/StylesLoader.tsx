import React, { useState, FC, ReactNode } from 'react';
import { Skeleton } from '@mui/material';

import { Root, ToolbarRoot } from './styless';

const StylesLoader: FC = () => {
  const [skeletons] = useState(new Array(7).fill(<Skeleton width="100%" />, 0, 7));

  return (
    <>
      <ToolbarRoot>
        <Skeleton width="100%" />
      </ToolbarRoot>
      <Root>{skeletons.map((skeleton: ReactNode) => skeleton)}</Root>
    </>
  );
};

export default StylesLoader;
