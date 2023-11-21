<?php

namespace App\Services\Alerts;

use App\Filter\Alerts\AlertFilter;
use App\Repository\Alerts\AlertRepository;

class AlertService
{
    public function __construct(private AlertRepository $alertRepository)
    {
    }

    public function create_alert($data)
    {
        return $this->alertRepository->create_alert($data);
    }
    public function getMyAlerts(AlertFilter $alertFilter = null)
    {
        if ($alertFilter != null)
            return $this->alertRepository->getFilterItems($alertFilter);
        else
            return $this->alertRepository->paginate();
    }

    public function all_alerts(AlertFilter $alertFilter = null)
    {
        if ($alertFilter != null)
            return $this->alertRepository->all_alerts($alertFilter);
        else
            return $this->alertRepository->paginate();
    }
}
