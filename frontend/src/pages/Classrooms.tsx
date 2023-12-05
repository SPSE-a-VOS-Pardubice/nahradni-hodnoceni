import {useContext} from 'react';
import './Dashboard.css';

import ClassroomTable from '../components/dashboard/ClassroomTable';
import {ClassroomsContext} from '../contexts/ClassroomsContext';

const ClassroomsPage = () => {
  const classroomsContext = useContext(ClassroomsContext);

  if (classroomsContext.id === 'FETCHING') {
    return <>Čekání na data</>;
  }
  if (classroomsContext.id === 'FAILED') {
    return <>Něco se pokazilo</>;
  }

  return (
    <>
      <ClassroomTable />
    </>
  );
};

export default ClassroomsPage;
