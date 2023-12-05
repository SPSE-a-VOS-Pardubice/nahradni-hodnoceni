import React, {createContext, useEffect, useState} from 'react';
import {fetchClassrooms} from '../services/APIService';
import StatusWrapper from '../models/StatusWrapper';
import Classroom from '../models/data/Classroom';

export type ClassroomsContextType = {
  data: Classroom[],
  // eslint-disable-next-line no-unused-vars
  setData: (newData: Classroom[]) => void
};

export const ClassroomsContext = createContext<StatusWrapper<ClassroomsContextType>>({id: 'FETCHING'});

export const ClassroomsContextProvider = (props: {
  children: React.ReactNode
}) => {
  const [statusWrappingData, setStatusWrappingData] = useState<StatusWrapper<Classroom[]>>({id: 'FETCHING'});

  useEffect(() => {
    setStatusWrappingData({id: 'FETCHING'});
    fetchClassrooms()
      .then(newData => setStatusWrappingData({id: 'SUCCESS', content: newData}))
      .catch(reason => setStatusWrappingData({id: 'FAILED', message: reason}));
  }, []);

  let providerValue: StatusWrapper<ClassroomsContextType>;
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
    <ClassroomsContext.Provider value={providerValue}>{props.children}</ClassroomsContext.Provider>
  );
};
