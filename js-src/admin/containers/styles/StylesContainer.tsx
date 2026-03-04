import React, { useEffect, FC } from 'react';
import { useDispatch, useSelector } from 'react-redux';

import Styles from '../../components/styles/Styles';
import { getStylesData } from '../../redux/modules/styles/actions';
import StylesLoader from '../../components/styles/StylesLoader';
import { TRootState } from '../../redux';

const StylesContainer: FC = () => {
  const dispatch = useDispatch();

  const filter = useSelector((state: TRootState) => state.styles.filter);
  const needReload = useSelector((state: TRootState) => state.styles.needReload);
  const loading = useSelector((state: TRootState) => state.styles.loading);

  useEffect(() => {
    if (!loading && needReload) {
      void getStylesData(filter)(dispatch).then();
    }
  }, [dispatch, filter, loading, needReload]);

  if (loading) return <StylesLoader />;

  return <Styles />;
};

export default StylesContainer;
