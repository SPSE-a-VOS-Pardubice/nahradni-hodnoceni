import React, {createContext, useState} from 'react';
import {getCurrentPeriod} from '../services/ExamService';

export type Period = {
  year: number,
  period: 1 | 2
}

export type PeriodContextType = {
  data: Period,
  setData: (newData: Period) => void
};

// we can disable type checking because PeriodContext.Provider instance is only created with non-undefined data
// eslint-disable-next-line @typescript-eslint/ban-ts-comment
// @ts-expect-error
export const PeriodContext = createContext<PeriodContextType>(undefined);

export const PeriodContextProvider = (props: {
  children: React.ReactNode
}) => {
  const [data, setData] = useState<Period>(getCurrentPeriod());

  return (
    <PeriodContext.Provider value={{data, setData}}>{props.children}</PeriodContext.Provider>
  );
};
