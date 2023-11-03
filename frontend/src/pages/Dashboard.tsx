import {useContext} from 'react';
import './Dashboard.css';

import {ExamsContext} from '../contexts/ExamsContext';
import DashboardTable from '../components/home/DashboardTable';
import DashboardStats from '../components/home/DashboardStats';

const DashboardPage = () => {
  const examsContext = useContext(ExamsContext);

  if (examsContext.id === 'FETCHING') {
    return <>Čekání na data</>;
  }
  if (examsContext.id === 'FAILED') {
    return <>Něco se pokazilo</>;
  }
  const exams = examsContext.content;

  console.log(exams);

  return (
    <>
      <DashboardStats />
      <DashboardTable />
    </>
  );
};

export default DashboardPage;
