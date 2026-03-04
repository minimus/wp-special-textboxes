import React, { ChangeEvent, FC } from 'react';
import { useSelector, useDispatch } from 'react-redux';
import { useNavigate } from 'react-router-dom';
import Radio from '@mui/material/Radio';
import RadioGroup from '@mui/material/RadioGroup';
import FormControlLabel from '@mui/material/FormControlLabel';
import Fab from '@mui/material/Fab';
import AddIcon from '@mui/icons-material/Add';

import { STYLES_CHANGE_FILTER } from '../../redux/constants';
import type { ILocale, TSettings, TStyle } from '../../../types/admin';
import { GlobalCommonStyles, GlobalCoreStyles, GlobalColorStyles } from '../styles';
import { TRootState } from '../../redux';

import { Root, ToolbarRoot } from './styless';
import StyleItem from './StyleItem';

const Styles: FC = () => {
  const dispatch = useDispatch();
  const navigate = useNavigate();

  const styles: TStyle[] | null = useSelector((state: TRootState) => state.styles.styles);
  const filter: number = useSelector((state: TRootState) => state.styles.filter);
  const settings: TSettings = useSelector((state: TRootState) => state.settings.settings);
  const localesData: ILocale | null = useSelector((state: TRootState) => state.locales.data);

  const { styles: { filterAll = '', filterActive = '', filterTrash = '', itemAlias = '', itemType = '', noData = '' } = {} } = localesData ?? {};

  const handleChange = (event: ChangeEvent<HTMLInputElement>) => {
    dispatch({ type: STYLES_CHANGE_FILTER, payload: Number(event.target.value) });
  };

  const onNewStyleClick = () => {
    void navigate('/editor');
  };

  return (
    <GlobalColorStyles settings={settings} styles={styles ?? []}>
      <GlobalCoreStyles />
      <GlobalCommonStyles settings={settings} />

      <ToolbarRoot>
        <RadioGroup aria-label="filter" name="filter" value={filter} onChange={handleChange} row>
          <FormControlLabel
            className="stb-styles-filter-label"
            value={0}
            control={<Radio color="primary" />}
            label={filterAll}
            labelPlacement="end"
          />
          <FormControlLabel
            className="stb-styles-filter-label"
            value={1}
            control={<Radio color="primary" />}
            label={filterActive}
            labelPlacement="end"
          />
          <FormControlLabel
            className="stb-styles-filter-label"
            value={2}
            control={<Radio color="primary" />}
            label={filterTrash}
            labelPlacement="end"
          />
        </RadioGroup>
      </ToolbarRoot>
      <Root>
        {!styles?.length ? (
          <span>{noData}</span>
        ) : (
          styles?.map((item: TStyle) => (
            <StyleItem
              key={item.slug}
              trash={item.trash ? 1 : 0}
              type={item.type}
              slug={item.slug}
              caption={item.caption}
              text={[itemAlias, itemType]}
              colors={item.colors}
              captionBg={!!settings.side}
            />
          ))
        )}
      </Root>
      {!!styles?.length && filter < 2 && (
        <Fab color="primary" style={{ position: 'fixed', bottom: 50, right: 40 }} onClick={onNewStyleClick}>
          <AddIcon />
        </Fab>
      )}
    </GlobalColorStyles>
  );
};

export default Styles;
