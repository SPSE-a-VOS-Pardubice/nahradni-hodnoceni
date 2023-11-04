import React, {createContext, useContext, useEffect, useState} from 'react';
import Exam from '../models/data/Exam';
import {fetchExams} from '../services/APIService';
import {PeriodContext} from './PeriodContext';
import StatusWrapper from '../models/StatusWrapper';

export type ExamsContextType = {
  data: Exam[],
  // eslint-disable-next-line no-unused-vars
  setData: (newData: Exam[]) => void
};

export const ExamsContext = createContext<StatusWrapper<ExamsContextType>>({id: 'FETCHING'});

export const ExamsContextProvider = (props: {
  children: React.ReactNode
}) => {
  const periodContext = useContext(PeriodContext);
  const [statusWrappingData, setStatusWrappingData] = useState<StatusWrapper<Exam[]>>({id: 'FETCHING'});

  console.log(statusWrappingData);

  useEffect(() => {
    setStatusWrappingData({id: 'FETCHING'});
    fetchExams(periodContext.data)
      .then(newData => setStatusWrappingData({id: 'SUCCESS', content: newData}))
      .catch(reason => setStatusWrappingData({id: 'FAILED', message: reason}));
  }, [periodContext]);

  let providerValue: StatusWrapper<ExamsContextType>;
  switch (statusWrappingData.id) {
  case 'FETCHING':
    providerValue = {id: 'FETCHING'};
    break;
  case 'SUCCESS':
    providerValue = {id: 'SUCCESS', content: {
      data: statusWrappingData.content,
      setData: (newData) => setStatusWrappingData({id: 'SUCCESS', content: newData}),
    }};
    break;
  case 'FAILED':
    providerValue = {id: 'FAILED', message: statusWrappingData.message};
    break;
  }

  return (
    <ExamsContext.Provider value={providerValue}>{props.children}</ExamsContext.Provider>
  );
};
