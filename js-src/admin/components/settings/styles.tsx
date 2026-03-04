import styled from 'styled-components';

export const Container = styled.section`
  min-height: 500px;
  padding: 10px;
  margin: 10px;
`;

export const Root = styled.div`
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;

  @media (max-width: 1641px) {
    display: flex;
    flex-direction: column;
  }
`;

export const SettingsSectionRoot = styled.div`
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  justify-content: flex-start;
  padding: 0;
  margin: 10px;
  border: 1px solid #dcdada;
  border-radius: 5px;
  box-shadow: 0 0 4px #0000001a;
`;

export const SettingsSectionCaption = styled.div`
  display: flex;
  align-items: center;
  justify-content: flex-start;
  width: 100%;
  padding: 5px 10px;
  font-weight: 700;
  border-bottom: 1px solid #dcdada;
  border-radius: 5px 5px 0 0;
`;

export const SettingsSectionBody = styled.div`
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  justify-content: flex-start;
  width: 100%;
  padding: 10px;
`;
