@php namespace {namespace}\Models;

use CodeIgniter\Model;

/**
 * {! name !} Model
 */
class {! name !} extends Model
{
    protected $table      = '{! table !}';
    protected $primaryKey = '{! primaryKey !}';

    protected $allowedFields = [{! allowedFields !}];

    protected $returnType = '{! returnType !}';
    protected $useSoftDeletes = {! useSoftDeletes !};

    protected $useTimestamps = {! useTimestamps !};
    protected $createdField  = '{! createdField !}';
    protected $updatedField  = '{! updatedField !}';
    protected $dateFormat    = '{! dateFormat !}';

    protected $validationRules    = {! validationRules !};
    protected $validationMessages = [];
    protected $skipValidation     = false;
}
