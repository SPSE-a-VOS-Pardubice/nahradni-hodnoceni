import React, {createContext, useEffect, useState} from 'react';
import {fetchTeachers} from '../services/APIService';
import StatusWrapper from '../models/StatusWrapper';
import Teacher from '@/models/data/Teacher';

export type TeachersContextType = {
  data: Teacher[],
  // eslint-disable-next-line no-unused-vars
  setData: (newData: Teacher[]) => void
};

export const TeachersContext = createContext<StatusWrapper<TeachersContextType>>({id: 'FETCHING'});

export const TeachersContextProvider = (props: {
  children: React.ReactNode
}) => {
  const [statusWrappingData, setStatusWrappingData] = useState<StatusWrapper<Teacher[]>>({id: 'FETCHING'});

  useEffect(() => {
    setStatusWrappingData({id: 'FETCHING'});
    fetchTeachers()
      .then(newData => setStatusWrappingData({id: 'SUCCESS', content: newData}))
      .catch(reason => setStatusWrappingData({id: 'FAILED', message: reason}));
  }, []);

  let providerValue: StatusWrapper<TeachersContextType>;
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
    <TeachersContext.Provider value={providerValue}>{props.children}</TeachersContext.Provider>
  );
};
