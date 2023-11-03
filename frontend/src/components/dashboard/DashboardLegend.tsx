import {useContext, useEffect, useState} from 'react';
import './DashboardLegend.css';
import {Period, PeriodContext} from '../../contexts/PeriodContext';
import StatusWrapper from '../../models/StatusWrapper';
import {fetchOldestYear} from '../../services/APIService';
import {getCurrentPeriod} from '../../services/ExamService';

const formatPeriod = (period: Period) => {
  // TODO use intl instead
  return `${period.year}/${period.year + 1} - ${period.period}. pololetí`;
};

const arrayRange = (start: number, stop: number) => Array.from(
  {length: (stop - start) + 1},
  (_value, index) => start + index,
);

const DashboardLegend = () => {
  const {data: currentPeriod, setData: setCurrentPeriod} = useContext(PeriodContext);
  const [statusWrappedOldestYear, setOldestYear] = useState<StatusWrapper<number | null>>({id: 'FETCHING'});

  useEffect(() => {
    setOldestYear({id: 'FETCHING'});
    fetchOldestYear()
      .then(newData => setOldestYear({id: 'SUCCESS', content: newData}))
      .catch(reason => setOldestYear({id: 'FAILED', message: reason}));
  }, []);

  let buttonContent;
  let options;
  switch (statusWrappedOldestYear.id) {
    case 'FETCHING':
      buttonContent = (
        <>načítání</>
      );
      options = (<></>);
      break;
    case 'SUCCESS':
      buttonContent = (
        <>{formatPeriod(currentPeriod)}</>
      );
      options = (
        <>
          {arrayRange(
            (statusWrappedOldestYear.content || 2000) * 2,
            (getCurrentPeriod().year * 2) + (getCurrentPeriod().period - 1),
          ).map(year => {
            const period = {year: Math.floor(year / 2), period: (year % 2) + 1} as Period;
            return (
              <option onClick={() => setCurrentPeriod(period)} key={JSON.stringify(period)}>{formatPeriod(period)}</option>
            );
          })}
        </>
      );
      break;
    case 'FAILED':
      buttonContent = (
        <>něco se pokazilo</>
      );
      options = (<></>);
      break;
  }

  return (
    <div className="half_year_config_part">
      <button className="select" name="half_year_config_select" id="half_year_config_select">
      <span>{buttonContent}<i className="fa-solid fa-angle-down"></i></span>
          <div className="dropdown">{options}</div>
      </button>

      <section className="app_colors_meaning col p-0">
        <article id="succeeded" className="color_meaning_art">
          <p>Úspěšně</p>
          <div className="color_rect"></div>
        </article>
        <article id="failed" className="color_meaning_art">
          <p>Neúspěšně</p>
          <div className="color_rect"></div>
        </article>
        <article id="unmarked" className="color_meaning_art">
          <p>Nehodnoceno</p>
          <div className="color_rect"></div>
        </article>
      </section>
    </div>
  );
};

export default DashboardLegend;
