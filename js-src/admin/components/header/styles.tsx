import styled from 'styled-components';

export const HeaderRoot = styled.header`
  display: flex;
  grid-area: header;
  gap: 4px;
  align-items: center;
  padding: 10px;

  & h1 {
    margin: 0;
  }
`;

export const SysInfoContainer = styled.div`
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 0 5px;

  & span {
    font-size: 12px;
  }
`;
