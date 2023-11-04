import {useContext, useEffect, useState} from 'react';
import './DashboardLegend.css';
import {Period, PeriodContext} from '../../contexts/PeriodContext';
import StatusWrapper from '../../models/StatusWrapper';
import {fetchPeriodRange} from '../../services/APIService';
import {PeriodRange} from '../../models/PeriodRange';

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
  const [statusWrappedPeriodRange, setStatusWrappedPeriodRange] = useState<StatusWrapper<PeriodRange | null>>({id: 'FETCHING'});

  useEffect(() => {
    setStatusWrappedPeriodRange({id: 'FETCHING'});
    fetchPeriodRange()
      .then(newData => setStatusWrappedPeriodRange({id: 'SUCCESS', content: newData}))
      .catch(reason => setStatusWrappedPeriodRange({id: 'FAILED', message: reason}));
  }, []);

  let buttonContent;
  let options;
  if (statusWrappedPeriodRange.id === 'FETCHING') {
    buttonContent = (
      <>načítání</>
    );
    options = (<></>);
  } else if (statusWrappedPeriodRange.id === 'SUCCESS') {
    buttonContent = (
      <>{formatPeriod(currentPeriod)}</>
    );

    if (statusWrappedPeriodRange.content === null) {
      return (<></>);
    }

    const rangeStart = (statusWrappedPeriodRange.content.oldest.year * 2) + (statusWrappedPeriodRange.content.oldest.period - 1);
    const rangeEnd = (statusWrappedPeriodRange.content.latest.year * 2) + (statusWrappedPeriodRange.content.latest.period - 1);
    options = (
      <>
        {arrayRange(rangeStart, rangeEnd).map(i => {
          const period = {year: Math.floor(i / 2), period: (i % 2) + 1} as Period;
          return (
            <option onClick={() => setCurrentPeriod(period)} key={JSON.stringify(period)}>{formatPeriod(period)}</option>
          );
        })}
      </>
    );
  } else if (statusWrappedPeriodRange.id === 'FAILED') {
    buttonContent = (
      <>něco se pokazilo</>
    );
    options = (<></>);
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
