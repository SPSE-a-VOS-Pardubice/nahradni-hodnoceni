import {useContext} from 'react';
import './Dashboard.css';

import {ExamsContext} from '../contexts/ExamsContext';
import DashboardTable from '../components/dashboard/DashboardTable';
import DashboardStats from '../components/dashboard/DashboardStats';

const DashboardPage = () => {
  const examsContext = useContext(ExamsContext);

  if (examsContext.id === 'FETCHING') {
    return <>Čekání na data</>;
  }
  if (examsContext.id === 'FAILED') {
    return <>Něco se pokazilo</>;
  }

  return (
    <>
      <DashboardStats />
      <DashboardTable />
    </>
  );
};

export default DashboardPage;
